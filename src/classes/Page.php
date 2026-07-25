<?php

class Page {
    private static array $assets = [
        "js"         => [],
        "css"        => [],
        "components" => [],
        "images"     => [],
        "other"      => [],
    ];

    public static function loadAssets(array $directories, ?string $component = null): void {
        // Create component child array if it doesn't exist yet
        if ($component !== null && !isset(self::$assets['components'][$component])) {
            self::$assets['components'][$component] = [
                "js" => [],
                "css" => []
            ];
        }

        foreach ($directories as $directory) {
            $files = scandir($directory);
            
            foreach ($files as $file) {
                // Skip dirs
                if ($file === '.' || $file === '..') {
                    continue;
                }

                // Parse extension and load it to assets based on file type
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                switch ($extension) {
                    case 'js':
                        if ($component !== null) {
                            self::$assets['components'][$component]['js'][] = self::formatFilePath($file, $component);
                        }
                        else {
                            self::$assets['js'][] = self::formatFilePath($file);
                        }
                        break;
                    case 'css':
                        if ($component !== null) {
                            self::$assets['components'][$component]['css'][] = self::formatFilePath($file, $component);
                        }
                        else {
                            self::$assets['css'][] = self::formatFilePath($file);
                        }
                        break;
                }
            }
        } 
    }

    public static function getAssetsByType(string $type): array | null{ 
       switch ($type) {
           case 'js':
               return self::$assets['js'];
           case 'css':
               return self::$assets['css'];
            default:
                return null;
       } 
    }

    public static function getComponentAssets(string $component): array {
        return self::$assets['components'][$component] ?? [
            "js"  => [],
            "css" => [],
        ];
    }

    public static function getComponentAssetsByType(string $type): array {
        $assets = [];

        foreach(self::$assets['components'] as $component) {
            if (isset($component[$type])) {
                $assets = array_merge($assets, $component[$type]);
            }
        }
        return $assets;
    }

    public static function renderStyles(string $route) {
        foreach (Page::getAssetsByType('css') as $css) {
            $url = $css . "&page=$route";
            echo "<link rel=\"stylesheet\" href=\"$url\"></link>";
        }

        foreach (Page::getComponentAssetsByType('css') as $css) {
            echo "<link rel=\"stylesheet\" href=\"$css\">";
        }
    }

    public static function renderScripts(string $route) {
        foreach (Page::getAssetsByType('js') as $js) {
            $url = $js . "&page=$route";
            echo "<script src=\"$url\"></script>";
        }

        foreach (Page::getComponentAssetsByType('js') as $js) {
            echo "<script src=\"$js\"></script>";
        }
    }

    private static function formatFilePath(string $file, ?string $component = null): string {
        $params = [
            "file" => $file
        ];

        if ($component !== null) {
            $params['component'] = $component;
        }

        return '/asset.php?' . http_build_query($params);
    }
}