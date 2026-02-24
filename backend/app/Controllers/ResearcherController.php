<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\ResearcherService;
use RMS\Backend\Utils\Response;
use Exception;

class ResearcherController
{
    private ResearcherService $service;

    public function __construct()
    {
        $this->service = new ResearcherService();
    }

    public function index(): void
    {
        $data = $this->service->getAll();
        Response::success($data);
    }

    public function show(int $id): void
    {
        try {
            $data = $this->service->getById($id);
            Response::success($data);
        } catch (Exception $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $this->service->create($input);

            Response::success(['id' => $id], 'Researcher created', 201);
        } catch (Exception $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $this->service->update($id, $input);

            Response::success(null, 'Researcher updated');
        } catch (Exception $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->service->delete($id);
            Response::success(null, 'Researcher deleted');
        } catch (Exception $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}