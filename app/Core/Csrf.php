<?php

namespace App\Core;

/**
 * Cross-Site Request Forgery (CSRF) protection.
 *
 * Every state-changing (POST) form embeds a per-session token which is
 * validated before the controller action runs.
 */
class Csrf
{
    /**
     * Get (or create) the session CSRF token.
     */
    public static function token(): string
    {
        $token = Session::get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }
        return $token;
    }

    /**
     * Validate a submitted token against the session token.
     */
    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get('csrf_token');
        return is_string($token) && is_string($sessionToken)
            && hash_equals($sessionToken, $token);
    }

    /**
     * Abort the request when the token is missing or invalid.
     */
    public static function verifyRequest(): void
    {
        if (!self::validate($_POST['csrf_token'] ?? null)) {
            Logger::warning('security.csrf_failed', [
                'uri'    => $_SERVER['REQUEST_URI'] ?? '',
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            ]);
            http_response_code(419);
            View::render('errors/403', [
                'title'   => 'Invalid Request',
                'message' => 'Your session token has expired. Please go back, refresh the page and try again.',
            ]);
            exit;
        }
    }
}
