<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\UserManageService;
use RMS\Backend\Utils\Response;

class UserController
{
    private UserManageService $service;

    public function __construct(UserManageService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $data = $this->service->getAll();
        Response::success($data);
    }

    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error("Invalid JSON payload");
            return;
        }

        try {
            $userId = $this->service->create($input);
            Response::success(['user_id' => $userId], "User created successfully", 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
