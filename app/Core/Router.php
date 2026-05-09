<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void    { $this->add('GET', $path, $handler); }
    public function post(string $path, array $handler): void   { $this->add('POST', $path, $handler); }
    public function put(string $path, array $handler): void    { $this->add('PUT', $path, $handler); }
    public function delete(string $path, array $handler): void { $this->add('DELETE', $path, $handler); }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = '#^' . preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $route['path']) . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $route['handler'];
                (new $class())->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}