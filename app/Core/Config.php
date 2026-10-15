<?php

namespace App\Core;

/**
 * Tiny configuration repository.
 *
 * Loads the externalized array returned by config/config.php and
 * provides dot-notation access, e.g. Config::get('db.driver').
 */
class Config
{
    /** @var array|null Cached configuration */
    private static $data = null;

    /**
     * Load the configuration file (once).
     */
    private static function load(): array
    {
        if (self::$data === null) {
            self::$data = require __DIR__ . '/../../config/config.php';
        }
        return self::$data;
    }

    /**
     * Read a value with dot notation: 'db.mysql.host'.
     *
     * @param mixed $default Returned when the key does not exist.
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $value    = self::load();

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        return $value;
    }
}
