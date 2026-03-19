<?php

namespace RMS\Backend\Models;

use RMS\Backend\Core\Database;
use PDO;

class Quartile
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all quartiles.
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM quartiles");
        return $stmt->fetchAll();
    }

    /**
     * Get a quartile by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quartiles WHERE quartile_id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new quartile.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO quartiles (quartile_rank) VALUES (:quartile_rank)");
        $stmt->execute([
            'quartile_rank' => $data['quartile_rank']
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing quartile.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE quartiles SET quartile_rank = :quartile_rank WHERE quartile_id = :id");
        return $stmt->execute([
            'id' => $id,
            'quartile_rank' => $data['quartile_rank']
        ]);
    }

    /**
     * Delete a quartile.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM quartiles WHERE quartile_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if the quartile is being used in any publication.
     *
     * @param int $id
     * @return bool
     */
    public function isBeingUsed(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM publications WHERE quartile_id = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
