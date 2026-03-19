<?php

namespace RMS\Backend\Models;

use PDO;
use RMS\Backend\Core\Database;

class WorkloadDefinition
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all workload definitions.
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM workload_definitions ORDER BY workload_definition_id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get a workload definition by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM workload_definitions WHERE workload_definition_id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new workload definition.
     *
     * @param array $data
     * @return int The ID of the newly created workload definition
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO workload_definitions (topic, workload_score, applicable_year) 
                VALUES (:topic, :workload_score, :applicable_year)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'topic' => $data['topic'],
            'workload_score' => $data['workload_score'],
            'applicable_year' => $data['applicable_year'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing workload definition.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $updateFields = [];
        $params = ['id' => $id];

        if (isset($data['topic'])) {
            $updateFields[] = "topic = :topic";
            $params['topic'] = $data['topic'];
        }
        if (isset($data['workload_score'])) {
            $updateFields[] = "workload_score = :workload_score";
            $params['workload_score'] = $data['workload_score'];
        }
        if (isset($data['applicable_year'])) {
            $updateFields[] = "applicable_year = :applicable_year";
            $params['applicable_year'] = $data['applicable_year'];
        }

        if (empty($updateFields)) {
            return false;
        }

        $sql = "UPDATE workload_definitions SET " . implode(', ', $updateFields) . " WHERE workload_definition_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a workload definition.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM workload_definitions WHERE workload_definition_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if the workload definition is being used in any publication_authors.
     *
     * @param int $id
     * @return bool
     */
    public function isBeingUsed(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM publication_authors WHERE workload_definition_id = :id");
        $stmt->execute(['id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
