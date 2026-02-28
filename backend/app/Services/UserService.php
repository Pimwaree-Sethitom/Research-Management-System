<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\User;
use RMS\Backend\Exceptions\NotFoundException;

class UserService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getUserWithRoles(int $userId): array
    {
        $user = $this->userModel->findById($userId);

        if (!$user) {
            throw new NotFoundException("User not found");
        }

        $roles = $this->userModel->getRoles($userId);

        $user['roles'] = $roles;

        return $user;
    }
}