<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\QuartileService;
use RMS\Backend\Utils\Response;

class QuartileController
{
    private QuartileService $service;

    public function __construct(QuartileService $service)
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
                Response::error("Quartile not found", 404);
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

        if (empty($input['quartile_rank'])) {
            Response::error("quartile_rank is required", 400);
            return;
        }

        try {
            $id = $this->service->create($input);
            Response::success(['quartile_id' => $id], "Quartile created successfully", 201);
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

        if (empty($input['quartile_rank'])) {
            Response::error("quartile_rank is required", 400);
            return;
        }

        try {
            $success = $this->service->update($id, $input);
            if ($success) {
                Response::success(null, "Quartile updated successfully");
            } else {
                Response::error("Failed to update Quartile", 500);
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
                Response::error("Quartile not found", 404);
                return;
            }

            if ($this->service->isBeingUsed($id)) {
                Response::error("Cannot delete quartile: It is being used in publications", 400);
                return;
            }

            $success = $this->service->delete($id);
            if ($success) {
                Response::success(null, "Quartile deleted successfully");
            } else {
                Response::error("Failed to delete Quartile", 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
