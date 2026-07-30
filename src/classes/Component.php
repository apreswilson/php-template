<?php

class Component {
    private static string $component_base_path = __DIR__ . '/../components/';

    public static function render(string $component_name, array $props = []): void {
        $component_directory = self::$component_base_path . $component_name;
        $component_php_file  = $component_directory . '/component.php';

        if (file_exists($component_php_file)) {
            Page::loadAssets([$component_directory]);
            $props = new Props($props);
            require $component_php_file;
        }
    }
}