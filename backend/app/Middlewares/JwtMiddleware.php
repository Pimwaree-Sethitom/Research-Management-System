<?php

namespace RMS\Backend\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RMS\Backend\Services\UserService;
use RMS\Backend\Utils\Response;
use RMS\Backend\Exceptions\UnauthorizedException;

class JwtMiddleware
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function handle(): void
    {
        $token = $this->extractToken();

        $decoded = $this->validateToken($token);

        $user = $this->loadUser($decoded->sub);

        // Inject user into global request context
        $GLOBALS['auth_user'] = $user;
    }

    /**
     * Extract Bearer token from Authorization header
     */
    private function extractToken(): string
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new UnauthorizedException("Authorization header missing");
        }

        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            throw new UnauthorizedException("Invalid Authorization format");
        }

        return $matches[1];
    }

    /**
     * Decode and validate JWT token
     */
    private function validateToken(string $token): object
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key($_ENV['JWT_SECRET'], 'HS256')
            );

            return $decoded;

        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new UnauthorizedException("Token expired");

        } catch (\Exception $e) {
            throw new UnauthorizedException("Invalid token");
        }
    }

    /**
     * Load user from database and attach roles
     */
    private function loadUser(int $userId): array
    {
        $user = $this->userService->getUserWithRoles($userId);

        if (!$user['is_active']) {
            throw new UnauthorizedException("User account inactive");
        }

        return $user;
    }
}