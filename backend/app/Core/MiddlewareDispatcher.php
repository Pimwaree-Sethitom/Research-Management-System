<?php

namespace RMS\Backend\Core;

class MiddlewareDispatcher
{
    private array $middlewares = [];

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function dispatch(callable $controllerAction)
    {
        $next = $controllerAction;

        while ($middleware = array_pop($this->middlewares)) {
            $next = function () use ($middleware, $next) {
                return $middleware->handle($next);
            };
        }

        return $next();
    }
}