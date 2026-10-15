<?php

namespace App\Core;

/**
 * Structured application logger.
 *
 * Writes one JSON-encoded line per event to logs/app.log so that
 * key events (logins, CRUD operations, errors) can be audited and
 * debugged without exposing sensitive details to end users.
 *
 * Line format:
 *   [2026-10-15 12:00:00] [INFO] [auth.login_success] {"user":"admin"} message
 */
class Logger
{
    private const LEVELS = ['debug' => 0, 'info' => 1, 'warning' => 2, 'error' => 3];

    /**
     * Log a debug message.
     */
    public static function debug(string $channel, array $context = [], string $message = ''): void
    {
        self::log('debug', $channel, $context, $message);
    }

    /**
     * Log an informational event (the bulk of the audit trail).
     */
    public static function info(string $channel, array $context = [], string $message = ''): void
    {
        self::log('info', $channel, $context, $message);
    }

    /**
     * Log a warning.
     */
    public static function warning(string $channel, array $context = [], string $message = ''): void
    {
        self::log('warning', $channel, $context, $message);
    }

    /**
     * Log an error (exceptions, failed operations).
     */
    public static function error(string $channel, array $context = [], string $message = ''): void
    {
        self::log('error', $channel, $context, $message);
    }

    /**
     * Write a structured log line.
     */
    private static function log(string $level, string $channel, array $context, string $message): void
    {
        if (!Config::get('logging.enabled', true)) {
            return;
        }

        $threshold = self::LEVELS[Config::get('logging.level', 'debug')] ?? 0;
        if (self::LEVELS[$level] < $threshold) {
            return;
        }

        $path = Config::get('logging.path');
        $dir  = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Never leak secrets such as passwords into the log file.
        $context = self::maskSecrets($context);

        $line = sprintf(
            "[%s] [%s] [%s] %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $channel,
            json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $message
        );

        // Logging must never take the application down.
        @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Replace sensitive values with a placeholder before writing.
     */
    private static function maskSecrets(array $context): array
    {
        $sensitive = ['password', 'password_confirm', 'new_password', 'current_password', 'password_hash', 'token'];
        foreach ($context as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $context[$key] = '***';
            } elseif (is_array($value)) {
                $context[$key] = self::maskSecrets($value);
            }
        }
        return $context;
    }
}
