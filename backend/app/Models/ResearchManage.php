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

    /**
     * Create a new publication with its authors.
     *
     * @param array $data
     * @return int The new publication ID
     * @throws \Exception
     */
    public function createWithAuthors(array $data): int
    {
        // 1. Validate Publication Data
        if (empty($data['title_th']) && empty($data['title_en'])) {
            throw new \Exception("At least one title (Thai or English) is required");
        }

        try {
            $this->db->beginTransaction();

            // 2. Insert into publications
            $stmt = $this->db->prepare("
                INSERT INTO publications (
                    title_th, title_en, research_type_id, venue_name, publication_year,
                    issue_number, publish_start_date, publish_end_date, issn_isbn,
                    page_range, quartile_id, academic_quality, remark, abstract, reference_url
                ) VALUES (
                    :title_th, :title_en, :research_type_id, :venue_name, :publication_year,
                    :issue_number, :publish_start_date, :publish_end_date, :issn_isbn,
                    :page_range, :quartile_id, :academic_quality, :remark, :abstract, :reference_url
                )
            ");

            $stmt->execute([
                'title_th' => $data['title_th'] ?? null,
                'title_en' => $data['title_en'] ?? null,
                'research_type_id' => $data['research_type_id'] ?? null,
                'venue_name' => $data['venue_name'] ?? null,
                'publication_year' => $data['publication_year'] ?? null,
                'issue_number' => $data['issue_number'] ?? null,
                'publish_start_date' => $data['publish_start_date'] ?? null,
                'publish_end_date' => $data['publish_end_date'] ?? null,
                'issn_isbn' => $data['issn_isbn'] ?? null,
                'page_range' => $data['page_range'] ?? null,
                'quartile_id' => $data['quartile_id'] ?? null,
                'academic_quality' => $data['academic_quality'] ?? null,
                'remark' => $data['remark'] ?? null,
                'abstract' => $data['abstract'] ?? null,
                'reference_url' => $data['reference_url'] ?? null,
            ]);

            $publicationId = (int) $this->db->lastInsertId();

            // 3. Insert into publication_authors
            if (!empty($data['authors']) && is_array($data['authors'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO publication_authors (
                        publication_id, researcher_id, author_role, workload_definition_id,
                        contribution_ratio, workload_amount, fiscal_year, academic_year
                    ) VALUES (
                        :publication_id, :researcher_id, :author_role, :workload_definition_id,
                        :contribution_ratio, :workload_amount, :fiscal_year, :academic_year
                    )
                ");

                $addedResearchers = [];

                foreach ($data['authors'] as $author) {
                    // Validate author
                    if (!isset($author['researcher_id'])) {
                        throw new \Exception("researcher_id is required for all authors");
                    }

                    // Prevent duplicate authors for the same publication
                    if (in_array($author['researcher_id'], $addedResearchers)) {
                        continue; // Or throw \Exception if you prefer strict error handling
                    }

                    $stmt->execute([
                        'publication_id' => $publicationId,
                        'researcher_id' => $author['researcher_id'],
                        'author_role' => $author['author_role'] ?? null,
                        'workload_definition_id' => $author['workload_definition_id'] ?? null,
                        'contribution_ratio' => $author['contribution_ratio'] ?? null,
                        'workload_amount' => $author['workload_amount'] ?? null,
                        'fiscal_year' => $author['fiscal_year'] ?? null,
                        'academic_year' => $author['academic_year'] ?? null,
                    ]);

                    $addedResearchers[] = $author['researcher_id'];
                }
            }

            $this->db->commit();
            return $publicationId;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get a single publication by ID with its authors.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        // 1. Fetch publication basic info
        $stmt = $this->db->prepare("SELECT * FROM publications WHERE publication_id = :id");
        $stmt->execute(['id' => $id]);
        $publication = $stmt->fetch();

        if (!$publication) {
            return null;
        }

        // 2. Fetch authors
        $stmt = $this->db->prepare("
            SELECT pa.*, r.full_name
            FROM publication_authors pa
            JOIN researchers r ON r.researcher_id = pa.researcher_id
            WHERE pa.publication_id = :id
        ");
        $stmt->execute(['id' => $id]);
        $publication['authors'] = $stmt->fetchAll();

        return $publication;
    }

    /**
     * Update an existing publication and its authors.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function updateWithAuthors(int $id, array $data): bool
    {
        // 1. Validate Publication Data (if present)
        if (isset($data['title_th']) || isset($data['title_en'])) {
            if (empty($data['title_th']) && empty($data['title_en'])) {
                throw new \Exception("At least one title (Thai or English) is required");
            }
        }

        try {
            $this->db->beginTransaction();

            // 2. Update publication basic info
            // Construct dynamic SET clause based on provided fields
            $fieldsToUpdate = [];
            $params = ['id' => $id];
            
            $allowedFields = [
                'title_th', 'title_en', 'research_type_id', 'venue_name', 'publication_year',
                'issue_number', 'publish_start_date', 'publish_end_date', 'issn_isbn',
                'page_range', 'quartile_id', 'academic_quality', 'remark', 'abstract', 'reference_url'
            ];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $fieldsToUpdate[] = "$field = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (!empty($fieldsToUpdate)) {
                $sql = "UPDATE publications SET " . implode(', ', $fieldsToUpdate) . " WHERE publication_id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            // 3. Sync authors if provided
            if (isset($data['authors']) && is_array($data['authors'])) {
                // Delete existing authors
                $stmt = $this->db->prepare("DELETE FROM publication_authors WHERE publication_id = :id");
                $stmt->execute(['id' => $id]);

                // Insert new authors
                $stmt = $this->db->prepare("
                    INSERT INTO publication_authors (
                        publication_id, researcher_id, author_role, workload_definition_id,
                        contribution_ratio, workload_amount, fiscal_year, academic_year
                    ) VALUES (
                        :publication_id, :researcher_id, :author_role, :workload_definition_id,
                        :contribution_ratio, :workload_amount, :fiscal_year, :academic_year
                    )
                ");

                $addedResearchers = [];
                foreach ($data['authors'] as $author) {
                    if (!isset($author['researcher_id'])) {
                        throw new \Exception("researcher_id is required for all authors");
                    }

                    if (in_array($author['researcher_id'], $addedResearchers)) {
                        continue;
                    }

                    $stmt->execute([
                        'publication_id' => $id,
                        'researcher_id' => $author['researcher_id'],
                        'author_role' => $author['author_role'] ?? null,
                        'workload_definition_id' => $author['workload_definition_id'] ?? null,
                        'contribution_ratio' => $author['contribution_ratio'] ?? null,
                        'workload_amount' => $author['workload_amount'] ?? null,
                        'fiscal_year' => $author['fiscal_year'] ?? null,
                        'academic_year' => $author['academic_year'] ?? null,
                    ]);

                    $addedResearchers[] = $author['researcher_id'];
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Delete an existing publication.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM publications WHERE publication_id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
