<?php

// TODO: This probably need to dramatically change. What I think we should do is inside each page.php, have them inline it.
// And go for some kind of reach architecture with props as arguments.
class Component {
    private static string $component_base_path = __DIR__ . '/../components/';

    public static function render(string $component_name, array $props = []): void {
        $component_directory = self::$component_base_path . $component_name;
        $component_php_file  = $component_directory . '/' . $component_name . '.php';

        if (file_exists($component_php_file)) {
            extract($props);
            Page::loadAssets([$component_directory], $component_name);
            require $component_php_file;
        }
    }
}