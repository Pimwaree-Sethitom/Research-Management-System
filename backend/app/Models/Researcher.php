<?php

namespace RMS\Backend\Models;

use RMS\Backend\Core\Database;
use PDO;

class Researcher
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ---------- READ ALL ---------- */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM researchers ORDER BY researcher_id DESC"
        );
        return $stmt->fetchAll();
    }

    /* ---------- READ BY ID ---------- */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM researchers WHERE researcher_id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /* ---------- CREATE ---------- */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO researchers (name, name_department_eng, name_department_thai)
             VALUES (:name, :dept_eng, :dept_thai)"
        );

        $stmt->execute([
            'name'       => $data['name'],
            'dept_eng'   => $data['name_department_eng'],
            'dept_thai'  => $data['name_department_thai'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /* ---------- UPDATE ---------- */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE researchers
             SET name = :name,
                 name_department_eng = :dept_eng,
                 name_department_thai = :dept_thai
             WHERE researcher_id = :id"
        );

        return $stmt->execute([
            'id'         => $id,
            'name'       => $data['name'],
            'dept_eng'   => $data['name_department_eng'],
            'dept_thai'  => $data['name_department_thai'],
        ]);
    }

    /* ---------- DELETE ---------- */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM researchers WHERE researcher_id = :id"
        );

        return $stmt->execute(['id' => $id]);
    }
}