<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\ResearchService;
use RMS\Backend\Utils\Response;

class ResearchController
{
    private ResearchService $service;

    public function __construct(ResearchService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the research publications.
     *
     * @return void
     */
    public function index(): void
    {
        try {
            $data = $this->service->getAll();
            Response::success($data);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
