<?php
declare(strict_types=1);

$env = static function (string $key, string $default): string {
    $value = getenv($key);

    return $value === false || $value === '' ? $default : $value;
};

return [
    'host' => $env('DB_HOST', '127.0.0.1'),
    'port' => (int) $env('DB_PORT', '3306'),
    'database' => $env('DB_DATABASE', 'electronics_store'),
    'username' => $env('DB_USERNAME', 'root'),
    'password' => $env('DB_PASSWORD', ''),
    'charset' => $env('DB_CHARSET', 'utf8mb4'),
];
