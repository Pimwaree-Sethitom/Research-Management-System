<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\AuthService;
use RMS\Backend\Utils\Response;

class AuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $token = $this->authService->login(
            $input['email'],
            $input['password']
        );

        Response::success([
            'token' => $token
        ]);
    }
}