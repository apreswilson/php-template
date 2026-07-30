<?php

class Page {
    private static array $assets = [];

    public static function loadAssets(array $directories): void {
        foreach ($directories as $directory) {
            // Parse directory info
            $directory  = realpath($directory);
            $parts      = explode('/', $directory);
            $dir_type   = [
                "type" => $parts[count($parts) - 2], // What type of directory is it? 
                "name" => $parts[count($parts) - 1] // What is the name of the directory?
            ];

            // Does a component array exist in assets? If not create it.
            if (!isset(self::$assets[$dir_type['type']])) {
                self::$assets[$dir_type['type']] = [];
            }

            // Does component element exist inside components array? If not create it.
            if(!isset(self::$assets[$dir_type['type']][$dir_type['name']])) {
                self::$assets[$dir_type['type']][$dir_type['name']]  = [
                    "js"  => [],
                    "css" => []
                ];
            }

            // Get each file
            $files = scandir($directory);
            
            foreach ($files as $file) {
                // Skip dirs
                if ($file === '.' || $file === '..') {
                    continue;
                }

                // Parse extension and load it to assets based dir_type
                self::addRegistryFile($dir_type['type'], $dir_type['name'], $file);
            }
        } 
    }

    public static function importAssets(string $type): void {
        self::renderAssets(self::$assets, $type);
    }


    private static function renderAssets(array $assets, string $type): void {
        foreach ($assets as $key => $value) {

            if (is_array($value)) {
                self::renderAssets($value, $type);
                continue;
            }

            if (!str_ends_with($key, ".$type")) {
                continue;
            }

            if ($type === 'css') {
                echo "<link rel=\"stylesheet\" href=\"$value\">";
            }

            if ($type === 'js') {
                echo "<script src=\"$value\"></script>";
            }
        }
    }

    private static function addRegistryFile(string $type, string $name, string $file): void {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (!in_array($extension, ['js', 'css'])) {
            return;
        }

        if (isset(self::$assets[$type][$name][$extension][$file])) {
            return;
        }

        self::$assets[$type][$name][$extension][$file] = '/asset.php?' . http_build_query([
            "file" => $file,
            "type" => $type,
            "name" => $name
        ]);
    }
}