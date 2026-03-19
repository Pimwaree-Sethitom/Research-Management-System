<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\ResearchTypeService;
use RMS\Backend\Utils\Response;

class ResearchTypeController
{
    private ResearchTypeService $service;

    public function __construct(ResearchTypeService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        try {
            $data = $this->service->getAll();
            Response::success($data);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function show(int $id): void
    {
        try {
            $data = $this->service->getById($id);
            if (!$data) {
                Response::error("Research Type not found", 404);
                return;
            }
            Response::success($data);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function store(): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        if (empty($input['type_name'])) {
            Response::error("type_name is required", 400);
            return;
        }

        try {
            $id = $this->service->create($input);
            Response::success(['research_type_id' => $id], "Research Type created successfully", 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error("Invalid JSON payload: " . json_last_error_msg(), 400);
            return;
        }

        if (empty($input['type_name'])) {
            Response::error("type_name is required", 400);
            return;
        }

        try {
            $success = $this->service->update($id, $input);
            if ($success) {
                Response::success(null, "Research Type updated successfully");
            } else {
                Response::error("Failed to update Research Type", 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $data = $this->service->getById($id);
            if (!$data) {
                Response::error("Research Type not found", 404);
                return;
            }

            $success = $this->service->delete($id);
            if ($success) {
                Response::success(null, "Research Type deleted successfully");
            } else {
                Response::error("Failed to delete Research Type", 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
