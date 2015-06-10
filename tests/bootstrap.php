<?php

// Composer autoloader
require __DIR__ .'/../vendor/autoload.php';

error_reporting(E_ALL);

// Tests autoloader
spl_autoload_register(
    function($className) {
        if (strlen($className) > 6 && substr($className, 0, 6) === 'Tests\\') {
            // This class should be loaded using bootstrap's autoloader
            $trimmed = substr($className, 6);
            $filename = __DIR__
                . '/'
                . str_replace('\\', '/', $trimmed)
                . '.php';

            if (file_exists($filename)) {
                // Loading
                include_once $filename;
            }
        }
    }
);
