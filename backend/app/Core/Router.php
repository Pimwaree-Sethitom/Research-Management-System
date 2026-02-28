<?php

namespace RMS\Backend\Core;

use RMS\Backend\Core\Container;
use RMS\Backend\Core\MiddlewareDispatcher;

class Router
{
    private array $routes = [];
    private Container $container;

    // สำหรับ group
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddlewares = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP METHODS
    |--------------------------------------------------------------------------
    */

    public function get(string $uri, array $action, array $middlewares = []): void
    {
        $this->addRoute('GET', $uri, $action, $middlewares);
    }

    public function post(string $uri, array $action, array $middlewares = []): void
    {
        $this->addRoute('POST', $uri, $action, $middlewares);
    }

    public function put(string $uri, array $action, array $middlewares = []): void
    {
        $this->addRoute('PUT', $uri, $action, $middlewares);
    }

    public function delete(string $uri, array $action, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $middlewares);
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP (Prefix + Middleware)
    |--------------------------------------------------------------------------
    */

    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddlewares = $this->currentGroupMiddlewares;

        // Prefix
        if (isset($options['prefix'])) {
            $this->currentGroupPrefix .= $options['prefix'];
        }

        // Middleware
        if (isset($options['middleware'])) {
            $this->currentGroupMiddlewares = array_merge(
                $this->currentGroupMiddlewares,
                (array) $options['middleware']
            );
        }

        $callback($this);

        // Restore ค่าเดิม (รองรับ nested group)
        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddlewares = $previousMiddlewares;
    }

    /*
    |--------------------------------------------------------------------------
    | ADD ROUTE
    |--------------------------------------------------------------------------
    */

    private function addRoute(
        string $method,
        string $uri,
        array $action,
        array $middlewares
    ): void {

        $fullUri = $this->currentGroupPrefix . $uri;

        $this->routes[] = [
            'method' => $method,
            'uri' => $fullUri,
            'action' => $action,
            'middlewares' => array_merge(
                $this->currentGroupMiddlewares,
                $middlewares
            )
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DISPATCH
    |--------------------------------------------------------------------------
    */

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {

            if ($method !== $route['method']) {
                continue;
            }

            // รองรับ {id} parameter
            $pattern = preg_replace(
                '#\{[a-zA-Z_]+\}#',
                '([0-9]+)',
                $route['uri']
            );

            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                [$class, $methodName] = $route['action'];

                $controller = $this->container->get($class);

                $dispatcher = new MiddlewareDispatcher();

                // โหลด middleware
                foreach ($route['middlewares'] as $middlewareClass) {
                    $dispatcher->add(
                        $this->container->get($middlewareClass)
                    );
                }

                $dispatcher->dispatch(function () use (
                    $controller,
                    $methodName,
                    $matches
                ) {
                    call_user_func_array(
                        [$controller, $methodName],
                        $matches
                    );
                });

                return;
            }
        }

        $this->notFound();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found'
        ]);
    }
}