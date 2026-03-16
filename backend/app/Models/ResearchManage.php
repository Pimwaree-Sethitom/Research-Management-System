<?php

namespace RMS\Backend\Models;

use RMS\Backend\Core\Database;
use PDO;

class ResearchManage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all publications with their associated authors.
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "
            SELECT
                p.publication_id,
                p.title_th,
                p.title_en,
                p.research_type_id,
                p.venue_name,
                p.publication_year,
                p.issue_number,
                p.publish_start_date,
                p.publish_end_date,
                p.issn_isbn,
                p.page_range,
                p.quartile_id,
                p.academic_quality,
                p.remark,
                p.abstract,
                p.reference_url,
                GROUP_CONCAT(
                    CONCAT(r.full_name, ' (', pa.author_role, ')')
                    ORDER BY pa.publication_author_id
                    SEPARATOR ', '
                ) AS authors
            FROM publications p
            LEFT JOIN publication_authors pa
                ON pa.publication_id = p.publication_id
            LEFT JOIN researchers r
                ON r.researcher_id = pa.researcher_id
            GROUP BY p.publication_id;
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
