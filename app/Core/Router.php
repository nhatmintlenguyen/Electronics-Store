<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array|callable $handler): void
    {
        $this->add('GET', $uri, $handler);
    }

    public function post(string $uri, array|callable $handler): void
    {
        $this->add('POST', $uri, $handler);
    }

    public function add(string $method, string $uri, array|callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => '/' . trim($uri, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = '/' . trim($uri, '/');
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['uri'], $uri);
            if ($params === null) {
                continue;
            }

            $this->call($route['handler'], $params);
            return;
        }

        http_response_code(404);
        View::render('pages/not_found.php', [
            'page_title' => 'Không tìm thấy trang',
            'page_description' => 'Trang bạn yêu cầu không tồn tại.',
        ]);
    }

    private function match(string $routeUri, string $requestUri): ?array
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches) !== 1) {
            return null;
        }

        return array_filter(
            $matches,
            static fn (string|int $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function call(array|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler(...array_values($params));
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        $controller->{$action}(...array_values($params));
    }
}
