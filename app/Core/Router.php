<?php

namespace App\Core;

/**
 * Front-controller router.
 *
 * Maps clean URLs such as  /students/42/edit  onto
 * controller actions, e.g.:
 *
 *   $router->get('/students/{id}/edit', [StudentController::class, 'edit']);
 *
 * The same router works behind Apache (.htaccess rewrite) and the
 * PHP built-in / preview server (internal redirect to index.php).
 */
class Router
{
    /** @var array[] Registered routes */
    private $routes = [];

    /**
     * Register a GET route.
     *
     * @param callable|array $handler
     */
    public function get(string $pattern, $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /**
     * Register a POST route.
     *
     * @param callable|array $handler
     */
    public function post(string $pattern, $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, $handler): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    /**
     * Dispatch the current request to the matching handler.
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $regex = $this->compile($route['pattern']);
            if (preg_match($regex, $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Page Not Found']);
    }

    /**
     * Convert "/students/{id}" into a regular expression.
     */
    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#i';
    }

    /**
     * Call the route handler (class/method pair or closure).
     *
     * @param callable|array $handler
     */
    private function invoke($handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method($params);
        } else {
            $handler($params);
        }
    }
}
