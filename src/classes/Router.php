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
                return new RouterResponse([]); 
        }
    } 

    private static function get(string $url) {
        // Determine route to load
        $route = trim($url, '/');

        // If on home page
        if ($route === '') {
            $route = 'home';
        }

        // Get page file, this is used in src/layout/page/page.php
        $page = __DIR__ . "/../../src/pages/$route/page.php";

        if (!file_exists($page)) {
            echo "No page to load for now";
        }

        return new RouterResponse([
            "page"   => $page,
            "method" => 'GET',
            "route"  => $route,
            "data"   => []
        ]);
    }

    private static function post(string $url, array $payload): RouterResponse {
        [$route, $query_params] = explode('?', $url);
        echo $query_params;
        $route                  = trim($route, '/');
        // $controller             = __DIR__ . "/../../src/pages/$route/controller.php";
        parse_str($query_params, $props);
        $function   = $props['action'];
        $component  = $props['component'] ?? null; // This is only included in queries
        $controller = __DIR__ . "/../../src/" .
                    ($component !== null ? 'components/' : 'pages/') .
                    ($component !== null ? $component : $route) .
                    '/controller.php';

        require_once $controller;

        $namespace = ($component !== null ? 'Components\\' : 'Pages\\' ) . 
                    ($component !== null ? ucfirst($component) : ucfirst($route));
        $callable  = strtolower($namespace . '\\' . $function);

        if (!function_exists($callable)) {
            throw new Exception("Action '$function' not found");
        }

        return new RouterResponse([
            "page"   => $url,
            "method" => 'POST',
            "route"  => $route,
            "data"   => $callable(...$payload)
        ]);
    }
}
