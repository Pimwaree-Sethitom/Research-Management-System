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

    public function show(int $id): void
    {
        $user = $this->service->getById($id);
        
        if (!$user) {
            Response::error("User not found", 404);
            return;
        }

        Response::success($user);
    }

    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        try {
            $userId = $this->service->create($input);
            Response::success(['user_id' => $userId], "User created successfully", 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        try {
            $success = $this->service->update($id, $input);
            if ($success) {
                Response::success(null, "User updated successfully");
            } else {
                Response::error("User not found or update failed", 404);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $success = $this->service->delete($id);
            if ($success) {
                Response::success(null, "User deleted successfully");
            } else {
                Response::error("User not found or deletion failed", 404);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
