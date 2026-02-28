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
}