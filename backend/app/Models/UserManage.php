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

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
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

    public function getByIdWithDetails(int $userId): ?array
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
            WHERE u.user_id = :user_id
            GROUP BY u.user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            $user['roles'] = $user['roles'] ? explode(',', $user['roles']) : [];
        }

        return $user ?: null;
    }

    public function updateWithDetails(int $userId, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Get current user to find researcher_id
            $stmt = $this->db->prepare("SELECT researcher_id FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $userId]);
            $researcherId = $stmt->fetchColumn();

            if (!$researcherId) return false;

            // 2. Update researchers
            $stmt = $this->db->prepare("
                UPDATE researchers 
                SET full_name = :full_name,
                    department_name_th = :department_name_th,
                    department_name_en = :department_name_en
                WHERE researcher_id = :id
            ");
            $stmt->execute([
                'id' => $researcherId,
                'full_name' => $data['full_name'],
                'department_name_th' => $data['department_name_th'],
                'department_name_en' => $data['department_name_en']
            ]);

            // 3. Update users
            $fields = [
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? 1,
                'id' => $userId
            ];
            $sql = "UPDATE users SET email = :email, is_active = :is_active";
            
            if (!empty($data['password_hash'])) {
                $sql .= ", password_hash = :password_hash";
                $fields['password_hash'] = $data['password_hash'];
            }
            
            $sql .= " WHERE user_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fields);

            // 4. Update Roles (Sync)
            if (isset($data['roles'])) {
                // Delete existing roles
                $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :id");
                $stmt->execute(['id' => $userId]);

                foreach ($data['roles'] as $roleName) {
                    // Find/Create role
                    $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = :name");
                    $stmt->execute(['name' => $roleName]);
                    $roleId = $stmt->fetchColumn();

                    if (!$roleId) {
                        $stmt = $this->db->prepare("INSERT INTO roles (role_name) VALUES (:name)");
                        $stmt->execute(['name' => $roleName]);
                        $roleId = $this->db->lastInsertId();
                    }

                    // Assign role
                    $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
                    $stmt->execute(['user_id' => $userId, 'role_id' => $roleId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteWithDetails(int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Get researcher_id before deleting the user
            $stmt = $this->db->prepare("SELECT researcher_id FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $userId]);
            $researcherId = $stmt->fetchColumn();

            // 2. Delete user roles
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :id");
            $stmt->execute(['id' => $userId]);

            // 3. Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $userId]);

            // 4. Delete researcher (since our design is 1:1)
            if ($researcherId) {
                $stmt = $this->db->prepare("DELETE FROM researchers WHERE researcher_id = :id");
                $stmt->execute(['id' => $researcherId]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // --- Seeder & Legacy Helper Methods ---

    public function createResearcher(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO researchers (full_name, department_name_en, department_name_th)
            VALUES (:full_name, :dept_en, :dept_th)
        ");
        $stmt->execute([
            'full_name' => $data['name'] ?? 'System',
            'dept_en' => $data['name_department_eng'] ?? null,
            'dept_th' => $data['name_department_thai'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, researcher_id, is_active)
            VALUES (:email, :password_hash, :researcher_id, :is_active)
        ");
        $stmt->execute([
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'researcher_id' => $data['researcher_id'] ?? null,
            'is_active' => $data['is_active'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET email = :email, researcher_id = :rid, is_active = :is_active
            WHERE user_id = :id
        ");
        return $stmt->execute([
            'email' => $data['email'],
            'rid' => $data['researcher_id'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'id' => $id
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
            $roleId = $this->db->lastInsertId();
        }
        return (int) $roleId;
    }

    public function assignRole(int $userId, int $roleId): void
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
        $stmt->execute(['user_id' => $userId, 'role_id' => $roleId]);
    }
}
