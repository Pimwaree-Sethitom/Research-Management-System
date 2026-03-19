<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\WorkloadDefinitionService;
use RMS\Backend\Utils\Response;

class WorkloadDefinitionController
{
    private WorkloadDefinitionService $service;

    public function __construct(WorkloadDefinitionService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /workload-definitions
     */
    public function index(): void
    {
        $data = $this->service->getAll();
        Response::success($data, "Workload definitions retrieved successfully");
    }

    /**
     * GET /workload-definitions/{id}
     */
    public function show(int $id): void
    {
        $data = $this->service->getById($id);
        if ($data) {
            Response::success($data, "Workload definition retrieved successfully");
        } else {
            Response::error("Workload definition not found", 404);
        }
    }

    /**
     * POST /workload-definitions
     */
    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['topic']) || empty(trim($data['topic']))) {
            Response::error("Topic is required", 400);
            return;
        }

        if (!isset($data['workload_score'])) {
            Response::error("Workload score is required", 400);
            return;
        }

        try {
            $id = $this->service->create($data);
            Response::success(['workload_definition_id' => $id], "Workload definition created successfully", 201);
        } catch (\Exception $e) {
            Response::error("Failed to create workload definition: " . $e->getMessage(), 500);
        }
    }

    /**
     * PUT /workload-definitions/{id}
     */
    public function update(int $id): void
    {
        $existing = $this->service->getById($id);
        if (!$existing) {
            Response::error("Workload definition not found", 404);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['topic']) && empty(trim($data['topic']))) {
            Response::error("Topic cannot be empty", 400);
            return;
        }

        try {
            $success = $this->service->update($id, $data);
            if ($success) {
                Response::success(null, "Workload definition updated successfully");
            } else {
                Response::error("No changes made or update failed", 400);
            }
        } catch (\Exception $e) {
            Response::error("Failed to update workload definition: " . $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /workload-definitions/{id}
     */
    public function destroy(int $id): void
    {
        try {
            $existing = $this->service->getById($id);
            if (!$existing) {
                Response::error("Workload definition not found", 404);
                return;
            }

            if ($this->service->isBeingUsed($id)) {
                Response::error("Cannot delete workload definition: It is being used in publication authors", 400);
                return;
            }

            $success = $this->service->delete($id);
            if ($success) {
                Response::success(null, "Workload definition deleted successfully");
            } else {
                Response::error("Failed to delete workload definition", 500);
            }
        } catch (\Exception $e) {
            Response::error("Error: " . $e->getMessage(), 500);
        }
    }
}
