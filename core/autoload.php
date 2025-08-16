<?php
spl_autoload_register(function ($className) {
    $paths = [
        '../app/controllers/',
        '../app/models/',
        '../core/'
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    die("Autoloader error: Class '$className' not found.");
});
