<?php

class Page {
    private static array $assets = [
        "js"     => [],
        "css"    => [],
        "images" => [],
        "other"  => [],
    ];

    public static function loadAssets(array $directories): void {
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
                        self::$assets['js'][] = self::formatFilePath($file);
                        break;
                    case 'css':
                        self::$assets['css'][] = self::formatFilePath($file);
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

    private static function formatFilePath(string $file): string {
        return "/asset.php?file=$file";
    }
}