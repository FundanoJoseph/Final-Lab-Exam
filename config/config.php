<?php
/**
 * =============================================================
 *  Application Configuration (externalized)
 * =============================================================
 *  All environment-specific settings live here so the rest of
 *  the code base stays portable between local development,
 *  InfinityFree (or any shared host), and cloud deployments.
 *
 *  NEVER hard-code credentials inside controllers or models.
 * =============================================================
 */

$config = [

    // ---------------------------------------------------------
    // Application
    // ---------------------------------------------------------
    'app' => [
        'name'     => 'Student Information System',
        'env'      => getenv('SIS_ENV') ?: 'production', // 'production' | 'development'
        'base_url' => getenv('SIS_BASE_URL') ?: '',       // '' = auto-detect from the request
        'timezone' => 'Asia/Manila',
    ],

    // ---------------------------------------------------------
    // Database
    // ---------------------------------------------------------
    // Driver can be switched without touching any PHP code:
    //   'mysql'  – used on InfinityFree / any MySQL host (default)
    //   'sqlite' – handy for local demos (file-based, no server)
    // You can also force it with the SIS_DB_DRIVER environment variable.
    'db' => [
        'driver' => getenv('SIS_DB_DRIVER') ?: 'mysql',

        // MySQL / MariaDB credentials – replace with the values
        // shown in your InfinityFree "MySQL Databases" control panel.
        'mysql' => [
            'host'     => getenv('SIS_DB_HOST') ?: 'localhost',
            'port'     => getenv('SIS_DB_PORT') ?: '3306',
            'database' => getenv('SIS_DB_NAME') ?: 'if0_your_database',
            'username' => getenv('SIS_DB_USER') ?: 'your_username',
            'password' => getenv('SIS_DB_PASS') ?: 'your_password',
            'charset'  => 'utf8mb4',
        ],

        // SQLite database file (used when driver = 'sqlite').
        'sqlite' => [
            'path' => __DIR__ . '/../database/demo.sqlite',
        ],
    ],

    // ---------------------------------------------------------
    // Security
    // ---------------------------------------------------------
    'security' => [
        // Session hardening
        'session_name'            => 'SIS_SESSID',
        'session_lifetime'        => 60 * 60 * 2, // 2 hours idle timeout
        'session_regenerate_mins' => 15,          // rotate the session id periodically

        // Login throttling (brute-force protection)
        'login_max_attempts'  => 5,
        'login_lockout_secs'  => 60,

        // Password policy
        'password_min_length' => 6,
    ],

    // ---------------------------------------------------------
    // Observability
    // ---------------------------------------------------------
    'logging' => [
        'enabled'  => true,
        'path'     => __DIR__ . '/../logs/app.log',
        'level'    => getenv('SIS_LOG_LEVEL') ?: 'debug', // debug | info | warning | error
    ],
];

// ---------------------------------------------------------------
// Optional local overrides
// ---------------------------------------------------------------
// Create config/config.local.php (git-ignored) returning an array with
// just the keys you want to change, e.g.:
//
//     <?php return ['db' => ['driver' => 'sqlite']];
//
// It is merged over the defaults above – handy for local demos
// without touching the shared configuration.
// ---------------------------------------------------------------
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $overrides = require $localConfig;
    if (is_array($overrides)) {
        $merge = function (array $base, array $extra) use (&$merge) {
            foreach ($extra as $key => $value) {
                if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                    $base[$key] = $merge($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
            return $base;
        };
        $config = $merge($config, $overrides);
    }
}

return $config;
