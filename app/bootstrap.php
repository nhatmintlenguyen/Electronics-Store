<?php

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('ROUTES_PATH', BASE_PATH . '/routes');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('VIEW_PATH', RESOURCES_PATH . '/views');
define('STORAGE_PATH', BASE_PATH . '/storage');

spl_autoload_register(function (string $className): void {
    $prefix = 'App\\Core\\';
    if (str_starts_with($className, $prefix)) {
        $relativeClass = substr($className, strlen($prefix));
        $path = APP_PATH . '/Core/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($path)) {
            require_once $path;
            return;
        }
    }

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

require_once CONFIG_PATH . '/app.php';
require_once APP_PATH . '/Support/language.php';
require_once APP_PATH . '/Support/helpers.php';
