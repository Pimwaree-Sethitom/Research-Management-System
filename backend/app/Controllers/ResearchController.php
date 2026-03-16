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

    /**
     * Store a newly created research publication.
     *
     * @return void
     */
    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        try {
            $id = $this->service->create($input);
            Response::success(['publication_id' => $id], "Publication created successfully", 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
