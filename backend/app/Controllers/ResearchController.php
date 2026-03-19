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

    /**
     * Display the specified research publication.
     *
     * @param int $id
     * @return void
     */
    public function show(int $id): void
    {
        try {
            $data = $this->service->getById($id);
            if (!$data) {
                Response::error("Publication not found", 404);
                return;
            }
            Response::success($data);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified research publication.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        try {
            $success = $this->service->update($id, $input);
            if ($success) {
                Response::success(null, "Publication updated successfully");
            } else {
                Response::error("Failed to update publication", 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified research publication.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        try {
            // Check if exists first
            $data = $this->service->getById($id);
            if (!$data) {
                Response::error("Publication not found", 404);
                return;
            }

            $success = $this->service->delete($id);
            if ($success) {
                Response::success(null, "Publication deleted successfully");
            } else {
                Response::error("Failed to delete publication", 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
