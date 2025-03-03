<?php
// File: app/Core/Router.php
namespace App\Core;

class Router {
    protected $routes = [];

    // Register a route for a specific method and path
    public function add(string $method, string $path, callable $callback) {
        $this->routes[] = ['method' => strtoupper($method), 'path' => $path, 'callback' => $callback];
    }

    // Dispatch the request to the corresponding route callback
    public function dispatch(string $method, string $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['path'] === $uri) {
                return call_user_func($route['callback']);
            }
        }
        // Optionally, return a 404 if no route matches
        http_response_code(404);
        echo "404 Not Found";
    }
}
