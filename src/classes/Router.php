<?php

// Come back and probably centralize this logic structure somehow
class Router {
    public static function route(string $url): RouterResponse {
        $payload = json_decode(file_get_contents("php://input"), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case "GET":
                return self::get($url);
            case "POST":
                return self::post($url, $payload);
            default:
                return new RouterResponse(
                    page: "N/A",
                    method: "N/A",
                    route: "N/A",
                    data: []
                ); 
        }
    } 

    private static function get(string $url) {
        // Determine route to load
        $route = trim($url, '/');

        if ($route === '') {
            $route = 'home';
        }

        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $route)) { 
            $route = '404'; 
        }

        // Get page file, this is used in src/layout/page/page.php
        $page = __DIR__ . "/../../src/pages/$route/page.php";
        $real = realpath($page);

        if ($real === false || !str_starts_with($real, realpath(__DIR__ . '/../../src/pages'))) { 
            $route = '404'; 
        }

        return new RouterResponse(
            page   : $page,
            method : 'GET',
            route  : $route,
            data   : []
        );
    }

    private static function post(string $url, array $payload): RouterResponse {
        $parts        = explode('?', $url, 2);
        $route        = trim($parts[0], '/');
        $query_params = $parts[1] ?? '';

        parse_str($query_params, $props);

        $function  = $props['action'] ?? null;
        $component = $props['component'] ?? null;

        $valid_route     = preg_match('/^[a-zA-Z0-9_\-]+$/', $route);
        $valid_component = $component === null || preg_match('/^[a-zA-Z0-9_\-]+$/', $component);
        $valid_function  = $function !== null && preg_match('/^[a-zA-Z0-9_\-]+$/', $function);

        if (!$valid_route|| !$valid_component|| !$valid_function) {
            throw new Exception("Invalid request");
        }

        $base_dir = ($component !== null ? 'components/' : 'pages/');
        $target   = ($component !== null ? $component : $route);

        $controller = __DIR__ . "/../../src/$base_dir$target/controller.php";
        $real       = realpath($controller);
        $real_base  = realpath(__DIR__ . "/../../src/$base_dir");

        if ($real === false || $real_base === false || !str_starts_with($real, $real_base)) {
            throw new Exception("Invalid request");
        }

        require_once $controller;

        // All page controller.php files must have a Pages\ namespace
        // All component controller.php files must have a Components\ namespace
        $namespace = ($component !== null ? 'Components\\' : 'Pages\\') .
                    ($component !== null ? ucfirst($component) : ucfirst($route));

        $allowed_actions = $namespace . '\\ALLOWED_ACTIONS';

        if (!defined($allowed_actions)) {
            throw new Exception("Invalid request");
        }

        $allowed = constant($allowed_actions);

        if (!in_array($function, $allowed, true)) {
            throw new Exception("Invalid request");
        }

        $callable = strtolower($namespace . '\\' . $function);

        if (!function_exists($callable)) {
            throw new Exception("Invalid request");
        }

        return new RouterResponse(
            page   : $url,
            method : 'POST',
            route  : $route,
            data   : $callable(...$payload)
        );
    }
}
