<?php

namespace App\Core;

use App\Models\UserModel;

/**
 * Authentication & role-based access control (RBAC).
 *
 * Handles login throttling, secure password verification
 * (password_verify against password_hash), session fixation
 * prevention and role checks.
 */
class Auth
{
    /**
     * Attempt to log a user in.
     *
     * @return array{success: bool, message: string}
     */
    public static function attempt(string $username, string $password): array
    {
        // ---- brute-force throttling -------------------------------------
        $attempts = (int) Session::get('login_attempts', 0);
        $lastTry  = (int) Session::get('login_last_attempt', 0);
        $max      = (int) Config::get('security.login_max_attempts', 5);
        $lockout  = (int) Config::get('security.login_lockout_secs', 60);

        if ($attempts >= $max && (time() - $lastTry) < $lockout) {
            $wait = $lockout - (time() - $lastTry);
            Logger::warning('auth.login_throttled', ['username' => $username, 'wait' => $wait]);
            return ['success' => false, 'message' => "Too many failed attempts. Please try again in {$wait} seconds."];
        }

        Session::set('login_last_attempt', time());

        // ---- verify credentials -----------------------------------------
        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            Session::set('login_attempts', $attempts + 1);
            Logger::warning('auth.login_failed', ['username' => $username]);
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        // ---- success: rebuild the session id ----------------------------
        Session::regenerate();
        Session::set('login_attempts', 0);
        Session::set('user', [
            'id'       => (int) $user['id'],
            'username' => $user['username'],
            'full_name'=> $user['full_name'],
            'role'     => $user['role'],
        ]);

        $userModel->recordLogin((int) $user['id']);
        Logger::info('auth.login_success', ['user' => $user['username'], 'role' => $user['role']]);

        return ['success' => true, 'message' => 'Welcome back, ' . $user['full_name'] . '!'];
    }

    /**
     * Log the current user out.
     */
    public static function logout(): void
    {
        $user = self::user();
        if ($user) {
            Logger::info('auth.logout', ['user' => $user['username']]);
        }
        Session::destroy();
    }

    /**
     * The authenticated user record (from the session), or null.
     */
    public static function user(): ?array
    {
        $user = Session::get('user');
        return is_array($user) ? $user : null;
    }

    /**
     * True when a user is logged in.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Require an authenticated user – otherwise redirect to /login.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            header('Location: ' . url('login'));
            exit;
        }
    }

    /**
     * Require one of the given roles (RBAC guard).
     *
     * @param string|array $roles
     */
    public static function requireRole($roles): void
    {
        self::requireLogin();
        $roles = (array) $roles;
        $user  = self::user();

        if (!in_array($user['role'], $roles, true)) {
            Logger::warning('auth.access_denied', ['user' => $user['username'], 'required' => $roles]);
            http_response_code(403);
            View::render('errors/403', ['title' => 'Access Denied']);
            exit;
        }
    }

    /**
     * Change the current user's password (requires the current password).
     *
     * @return array{success: bool, message: string}
     */
    public static function changePassword(string $current, string $new, string $confirm): array
    {
        $user = self::user();
        if ($user === null) {
            return ['success' => false, 'message' => 'Not authenticated.'];
        }

        $userModel = new UserModel();
        $record    = $userModel->findById((int) $user['id']);

        if ($record === null || !password_verify($current, $record['password_hash'])) {
            Logger::warning('auth.password_change_failed', ['user' => $user['username']]);
            return ['success' => false, 'message' => 'Your current password is incorrect.'];
        }

        $min = (int) Config::get('security.password_min_length', 6);
        if (strlen($new) < $min) {
            return ['success' => false, 'message' => "The new password must be at least {$min} characters."];
        }
        if ($new !== $confirm) {
            return ['success' => false, 'message' => 'The new passwords do not match.'];
        }

        $userModel->updatePassword((int) $user['id'], password_hash($new, PASSWORD_DEFAULT));
        Session::regenerate();
        Logger::info('auth.password_changed', ['user' => $user['username']]);

        return ['success' => true, 'message' => 'Your password has been updated.'];
    }
}
