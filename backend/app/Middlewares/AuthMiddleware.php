<?php

namespace RMS\Backend\Middleware;

use RMS\Backend\Core\MiddlewareInterface;
use Exception;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new Exception("Unauthorized", 401);
        }

        

        return $next();
    }
}