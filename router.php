<?php
/**
 * Development-only router for PHP's built-in web server:
 *
 *     php -S localhost:8000 router.php
 *
 * Mimics the production .htaccess behaviour: real files are served
 * directly, everything else is routed through index.php.
 * On a real host (Apache + .htaccess) this file is not used.
 */

if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $file = __DIR__ . $path;

    // Let the built-in server serve real static files.
    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

require __DIR__ . '/index.php';
