<?php

namespace RMS\Backend\Models;

use RMS\Backend\Core\Database;
use PDO;

class UserManage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllWithDetails(): array
    {
        $sql = "
            SELECT 
                u.user_id,
                r.full_name,
                r.department_name_th,
                r.department_name_en,
                u.email,
                u.is_active,
                GROUP_CONCAT(ro.role_name) AS roles
            FROM users u
            JOIN researchers r ON r.researcher_id = u.researcher_id
            LEFT JOIN user_roles ur ON ur.user_id = u.user_id
            LEFT JOIN roles ro ON ro.role_id = ur.role_id
            GROUP BY u.user_id
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createWithDetails(array $data): int
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert into researchers
            $stmt = $this->db->prepare("
                INSERT INTO researchers (full_name, department_name_th, department_name_en)
                VALUES (:full_name, :department_name_th, :department_name_en)
            ");
            $stmt->execute([
                'full_name' => $data['full_name'],
                'department_name_th' => $data['department_name_th'],
                'department_name_en' => $data['department_name_en']
            ]);
            $researcherId = $this->db->lastInsertId();

            // 2. Insert into users
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password_hash, researcher_id, is_active)
                VALUES (:email, :password_hash, :researcher_id, :is_active)
            ");
            $stmt->execute([
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
                'researcher_id' => $researcherId,
                'is_active' => $data['is_active'] ?? 1
            ]);
            $userId = $this->db->lastInsertId();

            // 3. Handle Roles
            if (!empty($data['roles'])) {
                foreach ($data['roles'] as $roleName) {
                    // Check if role exists, if not create
                    $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = :role_name");
                    $stmt->execute(['role_name' => $roleName]);
                    $roleId = $stmt->fetchColumn();

                    if (!$roleId) {
                        $stmt = $this->db->prepare("INSERT INTO roles (role_name) VALUES (:role_name)");
                        $stmt->execute(['role_name' => $roleName]);
                        $roleId = $this->db->lastInsertId();
                    }

                    // Link role to user
                    $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
                    $stmt->execute([
                        'user_id' => $userId,
                        'role_id' => $roleId
                    ]);
                }
            }

            $this->db->commit();
            return (int) $userId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
