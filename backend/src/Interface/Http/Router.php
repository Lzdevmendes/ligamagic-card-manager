<?php

declare(strict_types=1);

namespace App\Interface\Http;

final class Router
{
    /** @var array<int, array{method:string, pattern:string, paramNames:string[], handler:callable}> */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $paramNames = [];
        $pattern = preg_replace_callback('/\{(\w+)\}/', static function (array $m) use (&$paramNames): string {
            $paramNames[] = $m[1];

            return '([^/]+)';
        }, $path);

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => '#^' . $pattern . '$#',
            'paramNames' => $paramNames,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method) {
                continue;
            }

            if (preg_match($route['pattern'], $request->path, $matches) === 1) {
                array_shift($matches);
                $params = array_combine($route['paramNames'], $matches);
                ($route['handler'])($request, $params);

                return;
            }
        }

        JsonResponse::error(404, 'Rota não encontrada.');
    }
}
