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
}
