<?php

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('VIEW_PATH', RESOURCES_PATH . '/views');
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once CONFIG_PATH . '/app.php';
require_once APP_PATH . '/Support/language.php';
require_once APP_PATH . '/Support/helpers.php';

spl_autoload_register(function (string $className): void {
    $paths = [
        APP_PATH . '/Controllers/' . $className . '.php',
        APP_PATH . '/Models/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
