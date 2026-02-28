<?php

namespace RMS\Backend\Middlewares;

use RMS\Backend\Core\MiddlewareInterface;
use RMS\Backend\Services\JwtService;
use Exception;

class AuthMiddleware implements MiddlewareInterface
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(callable $next)
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new Exception("Unauthorized", 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = $this->jwtService->validate($token);
            $_REQUEST['user'] = $decoded; // เก็บ user ไว้ใช้ต่อ
        } catch (\Exception $e) {
            throw new Exception("Invalid or expired token", 401);
        }

        return $next();
    }
}