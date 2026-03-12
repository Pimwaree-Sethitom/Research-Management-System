<?php

namespace RMS\Backend\Models;

use RMS\Backend\Core\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE user_id = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function getRoles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.role_name
            FROM roles r
            JOIN user_roles ur ON r.role_id = ur.role_id
            WHERE ur.user_id = :user_id
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash)
            VALUES (:email, :password)
        ");
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password_hash']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function ensureRole(string $roleName): int
    {
        $stmt = $this->db->prepare("
            SELECT role_id FROM roles WHERE role_name = :name
        ");
        $stmt->execute(['name' => $roleName]);
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            $stmt = $this->db->prepare("
                INSERT INTO roles (role_name) VALUES (:name)
            ");
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
}