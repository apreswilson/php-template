<?php

/**
 * Loads environment variables from the vars.env file.
 *
 * Reads key-value pairs from the vars.env file and stores them
 * in the PHP $_ENV superglobal for use throughout the application.
 *
 * @return void
 */
function loadEnv() {
    $file_path = '../vars.env';
    $vars = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($vars as $var) {
        [$key, $value] = explode('=', $var);
        $_ENV[$key] = $value;
    }
}

loadEnv();