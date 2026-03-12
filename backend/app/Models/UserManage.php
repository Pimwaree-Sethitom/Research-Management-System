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

    public function getUserWithRoles(int $userId): array
    {
        $sql = "
            SELECT 
                u.user_id,
                u.email,
                u.is_active,
                r.full_name,
                GROUP_CONCAT(ro.role_name) AS roles
            FROM users u
            LEFT JOIN researchers r ON u.researcher_id = r.researcher_id
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id
            LEFT JOIN roles ro ON ur.role_id = ro.role_id
            WHERE u.user_id = :user_id
            GROUP BY u.user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            $user['roles'] = $user['roles'] ? explode(',', $user['roles']) : [];
        }

        return $user ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, researcher_id)
            VALUES (:email, :password, :researcher_id)
        ");
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password_hash'],
            'researcher_id' => $data['researcher_id'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET researcher_id = :researcher_id,
                email = :email,
                is_active = :is_active
            WHERE user_id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'researcher_id' => $data['researcher_id'] ?? null,
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    public function ensureRole(string $roleName): int
    {
        $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = :name");
        $stmt->execute(['name' => $roleName]);
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            $stmt = $this->db->prepare("INSERT INTO roles (role_name) VALUES (:name)");
            $stmt->execute(['name' => $roleName]);
            $roleId = (int) $this->db->lastInsertId();
        }

        return (int) $roleId;
    }

    public function assignRole(int $userId, int $roleId): void
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO user_roles (user_id, role_id)
            VALUES (:user_id, :role_id)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }

    public function createResearcher(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO researchers (full_name, department_name_en, department_name_th)
            VALUES (:name, :dept_eng, :dept_thai)
        ");

        $stmt->execute([
            'name'       => $data['name'],
            'dept_eng'   => $data['name_department_eng'],
            'dept_thai'  => $data['name_department_thai'],
        ]);

        return (int) $this->db->lastInsertId();
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
}
