<?php

namespace App\Core;

/**
 * Request helper: safe access to GET/POST input with sanitisation.
 */
class Request
{
    /**
     * Raw request method (GET, POST, ...).
     */
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * True for POST requests.
     */
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Normalise a scalar value (trim + drop control characters).
     *
     * NOTE: values are stored raw and escaped on OUTPUT with e() –
     * escaping here as well would double-encode entities.
     */
    private static function sanitize($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            // Remove NUL and other control characters (keep newlines/tabs).
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
            return $value;
        }
        return $value;
    }

    /**
     * Read a POST field.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function post(string $key, $default = '')
    {
        $value = $_POST[$key] ?? $default;
        return is_array($value) ? array_map([self::class, 'sanitize'], $value) : self::sanitize($value);
    }

    /**
     * Read a query-string parameter.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function query(string $key, $default = '')
    {
        $value = $_GET[$key] ?? $default;
        return is_array($value) ? array_map([self::class, 'sanitize'], $value) : self::sanitize($value);
    }

    /**
     * All POST data, sanitised.
     */
    public static function allPost(): array
    {
        $clean = [];
        foreach ($_POST as $key => $value) {
            $clean[$key] = is_array($value)
                ? array_map([self::class, 'sanitize'], $value)
                : self::sanitize($value);
        }
        return $clean;
    }
}
