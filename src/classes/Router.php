<?php

class Router {
    public static function route(string $url): string {
        // Determine route to load
        $route = trim($url, '/');
        echo $route;

        // If on home page
        if ($route === '') {
            $route = 'home';
        }

        // Get page file, this is used in src/layout/page/page.php
        $page = __DIR__ . "/../../src/pages/$route/page.php";
        echo $page;

        if (!file_exists($page)) {
            echo "No page to load for now";
        }

        return $page;
    } 
}
