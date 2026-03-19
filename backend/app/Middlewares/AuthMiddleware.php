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
        $normalizedHeaders = array_change_key_case($headers, CASE_LOWER);

        if (!isset($normalizedHeaders['authorization'])) {
            throw new Exception("Unauthorized", 401);
        }

        $authHeader = $normalizedHeaders['authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = $this->jwtService->validate($token);
            $_REQUEST['user'] = $decoded;
        } catch (\Exception $e) {
            throw new Exception("Invalid or expired token", 401);
        }

        return $next();
    }
}