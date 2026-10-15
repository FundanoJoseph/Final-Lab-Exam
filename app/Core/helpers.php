<?php

/**
 * Global helper functions used across views and controllers.
 */

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\Session;

/**
 * HTML-escape output (XSS protection). ALWAYS use this in views.
 *
 * @param mixed $value
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Build an application URL, aware of sub-folder deployments.
 *   url('students/42')  ->  /subfolder/students/42
 */
function url(string $path = ''): string
{
    static $base = null;

    if ($base === null) {
        $configured = rtrim((string) Config::get('app.base_url', ''), '/');
        if ($configured !== '') {
            $base = $configured;
        } else {
            // Auto-detect the base path from the entry script so the app
            // works at the domain root AND inside a sub-folder.
            $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            $base   = rtrim(str_replace('\\', '/', dirname($script)), '/');
        }
    }

    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

/**
 * Absolute base URL (scheme + host), used for canonical links.
 */
function base_url(string $path = ''): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

/**
 * Shortcut: a single flash message to the user.
 */
function flash(string $type, string $message): void
{
    Session::flash($type, $message);
}

/**
 * Old (previously submitted) input for re-populating forms.
 *
 * @param mixed $default
 * @return mixed
 */
function old(string $key, $default = '')
{
    static $old = null;
    if ($old === null) {
        $old = Session::pullOldInput();
    }
    return $old[$key] ?? $default;
}

/**
 * Hidden CSRF input field – include in EVERY <form method="post">.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
}

/**
 * The current logged-in user, or null.
 */
function current_user(): ?array
{
    return Auth::user();
}

/**
 * Badge markup helper used across the tables.
 */
function status_badge(string $status): string
 {
    $map = [
        'Active'      => 'badge-success',
        'Enrolled'    => 'badge-success',
        'Passed'      => 'badge-success',
        'Completed'   => 'badge-success',
        'Graduated'   => 'badge-info',
        'In Progress' => 'badge-info',
        'Pending'     => 'badge-warning',
        'On Leave'    => 'badge-warning',
        'Incomplete'  => 'badge-warning',
        'Inactive'    => 'badge-secondary',
        'Transferred' => 'badge-secondary',
        'Dropped'     => 'badge-danger',
        'Failed'      => 'badge-danger',
    ];
    $class = $map[$status] ?? 'badge-secondary';
    return '<span class="badge ' . $class . '">' . e($status) . '</span>';
}

/**
 * Format a date string for display.
 */
function format_date(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $ts = strtotime($date);
    return $ts ? date('M j, Y', $ts) : e($date);
}

/**
 * Format a datetime for the activity lists.
 */
function format_datetime(?string $datetime): string
{
    if (!$datetime) {
        return '—';
    }
    $ts = strtotime($datetime);
    return $ts ? date('M j, Y g:i A', $ts) : e($datetime);
}
