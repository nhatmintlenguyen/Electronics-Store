<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    public function __construct(private Router $router)
    {
    }

    public function run(): void
    {
        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
        $basePath = parse_url(appBaseUrl(), PHP_URL_PATH) ?: '';

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        $route = trim($path, '/');
        if (str_starts_with($route, 'public/')) {
            $route = substr($route, strlen('public/'));
        }

        $this->router->dispatch($method, '/' . trim($route, '/'));
    }
}
