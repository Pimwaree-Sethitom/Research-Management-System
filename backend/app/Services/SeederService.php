<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\UserManage;

class SeederService
{
    private UserManage $userModel;

    public function __construct(UserManage $userModel)
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
            // 2. ถ้ายังไม่มี ให้สร้าง Researcher ใหม่สำหรับ Admin
            $researcherId = $this->userModel->createResearcher([
                'name' => 'Admin System',
                'name_department_eng' => 'Administration',
                'name_department_thai' => 'ฝ่ายบริหารจัดการ'
            ]);

            // 3. สร้าง User ใหม่และผูกกับ ResearcherId
            $userId = $this->userModel->create([
                'email' => $adminEmail,
                'password_hash' => password_hash($adminPass, PASSWORD_BCRYPT),
                'researcher_id' => $researcherId
            ]);

            // 4. ตรวจสอบและสร้าง Role 'admin'
            $roleId = $this->userModel->ensureRole('admin');

            // 5. ผูก Role กับ User
            $this->userModel->assignRole($userId, $roleId);
        } else if (empty($user['researcher_id'])) {
            // กรณีมี User แล้วแต่ยังไม่ได้ผูกกับ Researcher (เช่น จากข้อมูลเก่า)
            $researcherId = $this->userModel->createResearcher([
                'name' => 'Admin System',
                'name_department_eng' => 'Administration',
                'name_department_thai' => 'ฝ่ายบริหารจัดการ'
            ]);
            
            $this->userModel->update($user['user_id'], [
                'email' => $user['email'],
                'researcher_id' => $researcherId,
                'status' => $user['status']
            ]);
        }
    }
}
