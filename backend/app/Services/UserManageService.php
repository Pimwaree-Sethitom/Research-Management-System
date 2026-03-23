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

    public function getById(int $id): ?array
    {
        return $this->userModel->getByIdWithDetails($id);
    }

    public function create(array $data): int
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }

        // Map is_active to status for backward compatibility
        if (isset($data['is_active'])) {
            $data['status'] = $data['is_active'];
            unset($data['is_active']);
        }
        
        return $this->userModel->createWithDetails($data);
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }

        // Map is_active to status for backward compatibility
        if (isset($data['is_active'])) {
            $data['status'] = $data['is_active'];
            unset($data['is_active']);
        }

        return $this->userModel->updateWithDetails($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userModel->deleteWithDetails($id);
    }
}