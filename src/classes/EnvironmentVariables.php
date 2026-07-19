<?php

// Simple helper to load environment variables
class EnvironmentVariables {

    public static function loadEnvironmentVariables(string $file_path) {
        $vars = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($vars as $var) {
            [$key, $value] = explode('=', $var);
            $_ENV[$key] = $value;
        }
    }
}