<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\UserManage;
use Exception;

class AuthService
{
    private UserManage $userModel;
    private JwtService $jwtService;

    public function __construct(UserManage $userModel, JwtService $jwtService)
    {
        $this->userModel = $userModel;
        $this->jwtService = $jwtService;
    }

    public function login(string $email, string $password): string
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception("Invalid credentials", 401);
        }

        return $this->jwtService->generate([
            'user_id' => $user['user_id'],
            'email' => $user['email']
        ]);
    }
}