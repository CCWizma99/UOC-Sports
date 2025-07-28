<?php
class Router {
    private $routes = [];

    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute($method, $uri, $action) {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'action' => $action
        ];
    }

    public function dispatch($requestedUri, $method) {
        // Get path without query string
        $requestedUri = parse_url($requestedUri, PHP_URL_PATH);
        $requestedUri = '/' . trim($requestedUri, '/');

        // Adjust your project folder here
        $basePath = '/uoc-sports/public';

        // Remove base path
        if (str_starts_with($requestedUri, $basePath)) {
            $requestedUri = substr($requestedUri, strlen($basePath));
        }

        // Normalize
        $requestedUri = trim($requestedUri, '/');

        // Debug output (optional)
        // echo "Final URI: [$requestedUri] - Method: [$method]<br>";

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $requestedUri) {
                return $this->runAction($route['action']);
            }
        }

        http_response_code(404);
        echo "404 Not Found - No route matched.";
    }

    private function runAction($action) {
        list($controllerName, $methodName) = explode('@', $action);

        if (!class_exists($controllerName)) {
            die("Controller '$controllerName' not found.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            die("Method '$methodName' not found in controller '$controllerName'.");
        }

        return call_user_func([$controller, $methodName]);
    }
}
