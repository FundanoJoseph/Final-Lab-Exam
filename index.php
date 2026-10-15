<?php
/**
 * =============================================================
 *  Student Information System – front controller (entry point)
 * =============================================================
 *  Every request is routed through this file so that the
 *  application follows a clean MVC structure:
 *
 *      index.php  ->  Router  ->  Controller  ->  Model / View
 * =============================================================
 */

// ---------------------------------------------------------------
// 1. Bootstrap
// ---------------------------------------------------------------
declare(strict_types=1);

// Show errors only when developing (never leak details in production).
$debug = (getenv('SIS_ENV') ?: 'production') !== 'production';
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

// Report every PHP warning/notice to the log as well.
set_error_handler(function (int $errno, string $message, string $file, int $line) {
    \App\Core\Logger::error('php.error', [
        'errno'   => $errno,
        'message' => $message,
        'file'    => $file,
        'line'    => $line,
    ]);
    return false; // keep PHP's normal error handling as well
});

// ---------------------------------------------------------------
// 2. Autoloader (PSR-4 style: App\... maps to app/...)
// ---------------------------------------------------------------
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file     = __DIR__ . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/app/Core/helpers.php';

// ---------------------------------------------------------------
// 3. Error / exception handling (friendly page + internal log)
// ---------------------------------------------------------------
set_exception_handler(function (Throwable $e) {
    \App\Core\Logger::error('unhandled_exception', [
        'type'    => get_class($e),
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
        'trace'   => $e->getTraceAsString(),
    ]);

    http_response_code(500);
    try {
        \App\Core\View::render('errors/500', [
            'title'   => 'Something Went Wrong',
            'message' => 'An unexpected error occurred. It has been logged for review.',
        ]);
    } catch (Throwable $ignored) {
        echo 'Internal server error.';
    }
    exit;
});

// ---------------------------------------------------------------
// 4. Timezone + session
// ---------------------------------------------------------------
date_default_timezone_set(\App\Core\Config::get('app.timezone', 'UTC'));
\App\Core\Session::start();

// ---------------------------------------------------------------
// 5. Routes
// ---------------------------------------------------------------
$router = new \App\Core\Router();

// Authentication
$router->get('/login',   [\App\Controllers\AuthController::class, 'index']);
$router->post('/login',  [\App\Controllers\AuthController::class, 'login']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Dashboard
$router->get('/', [\App\Controllers\DashboardController::class, 'index']);

// Departments (Manage Department)
$router->get('/departments',               [\App\Controllers\DepartmentController::class, 'index']);
$router->get('/departments/create',        [\App\Controllers\DepartmentController::class, 'create']);
$router->post('/departments',              [\App\Controllers\DepartmentController::class, 'store']);
$router->get('/departments/{id}',          [\App\Controllers\DepartmentController::class, 'show']);
$router->get('/departments/{id}/edit',     [\App\Controllers\DepartmentController::class, 'edit']);
$router->post('/departments/{id}',         [\App\Controllers\DepartmentController::class, 'update']);
$router->post('/departments/{id}/delete',  [\App\Controllers\DepartmentController::class, 'delete']);

// Courses (Course Management)
$router->get('/courses',               [\App\Controllers\CourseController::class, 'index']);
$router->get('/courses/create',        [\App\Controllers\CourseController::class, 'create']);
$router->post('/courses',              [\App\Controllers\CourseController::class, 'store']);
$router->get('/courses/{id}',          [\App\Controllers\CourseController::class, 'show']);
$router->get('/courses/{id}/edit',     [\App\Controllers\CourseController::class, 'edit']);
$router->post('/courses/{id}',         [\App\Controllers\CourseController::class, 'update']);
$router->post('/courses/{id}/delete',  [\App\Controllers\CourseController::class, 'delete']);

// Students (Student Information and Records Management)
$router->get('/students',                [\App\Controllers\StudentController::class, 'index']);
$router->get('/students/create',         [\App\Controllers\StudentController::class, 'create']);
$router->post('/students',               [\App\Controllers\StudentController::class, 'store']);
$router->get('/students/{id}',           [\App\Controllers\StudentController::class, 'show']);
$router->get('/students/{id}/edit',      [\App\Controllers\StudentController::class, 'edit']);
$router->post('/students/{id}',          [\App\Controllers\StudentController::class, 'update']);
$router->post('/students/{id}/status',   [\App\Controllers\StudentController::class, 'updateStatus']);
$router->post('/students/{id}/delete',   [\App\Controllers\StudentController::class, 'delete']);

// Student academic history
$router->post('/students/{id}/academics',                 [\App\Controllers\StudentController::class, 'storeAcademic']);
$router->get('/students/{id}/academics/{aid}/edit',       [\App\Controllers\StudentController::class, 'editAcademic']);
$router->post('/students/{id}/academics/{aid}',           [\App\Controllers\StudentController::class, 'updateAcademic']);
$router->post('/students/{id}/academics/{aid}/delete',    [\App\Controllers\StudentController::class, 'deleteAcademic']);

// Settings / account
$router->get('/settings',          [\App\Controllers\AccountController::class, 'index']);
$router->post('/settings/password', [\App\Controllers\AccountController::class, 'changePassword']);

// ---------------------------------------------------------------
// 6. Dispatch
// ---------------------------------------------------------------
$router->dispatch(
    \App\Core\Request::method(),
    $_SERVER['REQUEST_URI'] ?? '/'
);
