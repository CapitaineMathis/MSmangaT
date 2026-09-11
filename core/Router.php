<?php

class Router {

    public function dispatch($url)
    {
        //gestion des controllers
        $url = trim($url, '/');
        $parts = explode('/', $url);

        $controller = !empty($parts[0]) ? $parts[0] : 'home';
        $controllerName = ucfirst($controller) . 'Controller';

        $method = !empty($parts[1]) ? $parts[1] : 'index';
        $params = array_slice($parts, 2);

        if (!class_exists($controllerName)) {
            http_response_code(404);
            echo "Error: Controller '$controllerName' not found.";
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo "Error: Method '$method' not found in controller '$controllerName'.";
            return;
        }

        try {
            call_user_func_array([$controller, $method], $params);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "Controller Execution Error: " . htmlspecialchars($e->getMessage());
        }
    }

}