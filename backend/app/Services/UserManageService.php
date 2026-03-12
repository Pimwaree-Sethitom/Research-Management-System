<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\UserManage;

class UserManageService
{
    private UserManage $userModel;

    public function __construct(UserManage $userModel)
    {
        $this->userModel = $userModel;
    }

    public function getAll(): array
    {
        return $this->userModel->getAllWithDetails();
    }

    public function getUserWithRoles(int $userId): array
    {
        return $this->userModel->getUserWithRoles($userId);
    }
}