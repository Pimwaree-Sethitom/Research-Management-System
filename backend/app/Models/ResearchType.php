<?php

namespace RMS\Backend\Models;

use PDO;

class ResearchType
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \RMS\Backend\Core\Database::getInstance();
    }

    /**
     * Get all research types.
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM research_types");
        return $stmt->fetchAll();
    }

    /**
     * Get a research type by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM research_types WHERE research_type_id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new research type.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO research_types (type_name) VALUES (:type_name)");
        $stmt->execute([
            'type_name' => $data['type_name']
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing research type.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE research_types SET type_name = :type_name WHERE research_type_id = :id");
        return $stmt->execute([
            'id' => $id,
            'type_name' => $data['type_name']
        ]);
    }

    /**
     * Delete a research type.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM research_types WHERE research_type_id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
