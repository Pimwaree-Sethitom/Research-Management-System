<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\ResearchManage;

class ResearchService
{
    private ResearchManage $researchModel;

    public function __construct(ResearchManage $researchModel)
    {
        $this->researchModel = $researchModel;
    }

    /**
     * Get all research publications.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->researchModel->getAll();
    }

    /**
     * Create a new research publication.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        return $this->researchModel->createWithAuthors($data);
    }

    /**
     * Get a single research publication by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->researchModel->getById($id);
    }

    /**
     * Update an existing research publication.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->researchModel->updateWithAuthors($id, $data);
    }
}
