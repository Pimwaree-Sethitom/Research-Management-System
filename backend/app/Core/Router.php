<?php

namespace RMS\Backend\Core;

use RMS\Backend\Core\Container;
use RMS\Backend\Core\MiddlewareDispatcher;

class Router
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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

    private function addRoute(
        string $method,
        string $uri,
        array $action,
        array $middlewares
    ): void {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {

            if ($method !== $route['method']) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z]+\}#', '([0-9]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                [$class, $methodName] = $route['action'];

                $controller = $this->container->get($class);

                //สร้าง dispatcher
                $dispatcher = new MiddlewareDispatcher();

                //ใส่ middleware ตาม route
                foreach ($route['middlewares'] as $middlewareClass) {
                    $dispatcher->add(
                        $this->container->get($middlewareClass)
                    );
                }

                //ห่อ controller call ด้วย closure
                $dispatcher->dispatch(function () use ($controller, $methodName, $matches) {
                    call_user_func_array([$controller, $methodName], $matches);
                });

                return;
            }
        }

        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found'
        ]);
    }
}