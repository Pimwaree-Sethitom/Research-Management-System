<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\User;

class SeederService
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function seedAdmin(): void
    {
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? null;
        $adminPass = $_ENV['ADMIN_PASSWORD'] ?? null;

        if (!$adminEmail || !$adminPass) {
            return;
        }

        // 1. ตรวจสอบว่ามี Admin หรือยัง
        $user = $this->userModel->findByEmail($adminEmail);

        if (!$user) {
            // 2. ถ้ายังไม่มี ให้สร้าง User ใหม่
            $userId = $this->userModel->create([
                'email' => $adminEmail,
                'password_hash' => password_hash($adminPass, PASSWORD_BCRYPT)
            ]);

            // 3. ตรวจสอบและสร้าง Role 'admin'
            $roleId = $this->userModel->ensureRole('admin');

            // 4. ผูก Role กับ User
            $this->userModel->assignRole($userId, $roleId);
        }
    }
}
