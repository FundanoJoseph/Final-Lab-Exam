<?php

namespace App\Core;

/**
 * Hardened session management.
 *
 * - uses a custom cookie name,
 * - HttpOnly + SameSite cookies,
 * - periodic session-id rotation,
 * - flash messaging and old-input helpers.
 */
class Session
{
    /** @var bool Whether the session has been started */
    private static $started = false;

    /**
     * Start the session with hardened cookie parameters.
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        // Fall back to the system temp dir when the configured save path is
        // missing/unwritable (e.g. some containerised sandboxes).
        $savePath = session_save_path();
        if ($savePath === '' || !is_dir($savePath) || !is_writable($savePath)) {
            @session_save_path(sys_get_temp_dir());
        }

        session_name(Config::get('security.session_name', 'SIS_SESSID'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
        self::$started = true;

        // Rotate the session id periodically to limit session-fixation risk.
        $lastRegen = $_SESSION['_last_regen'] ?? 0;
        $maxAge    = (int) Config::get('security.session_regenerate_mins', 15) * 60;
        if (time() - $lastRegen > $maxAge) {
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    /**
     * Force a brand-new session id (call on privilege change, e.g. login).
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_last_regen'] = time();
    }

    /**
     * Read a session value.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Write a session value.
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Remove a session value.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Store a one-shot flash message.
     * Type: 'success' | 'error' | 'warning' | 'info'
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Pull all pending flash messages (and clear them).
     */
    public static function pullFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    /**
     * Keep submitted input so forms can be re-populated after validation errors.
     */
    public static function flashOldInput(array $input): void
    {
        $_SESSION['_old_input'] = $input;
    }

    /**
     * Pull (and clear) the old input.
     */
    public static function pullOldInput(): array
    {
        $old = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        return $old;
    }

    /**
     * Completely destroy the session (logout).
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        self::$started = false;
    }
}
