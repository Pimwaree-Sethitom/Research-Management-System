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
}
