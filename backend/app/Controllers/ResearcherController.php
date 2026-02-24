<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\ResearcherService;
use RMS\Backend\Utils\Response;

class ResearcherController
{
    private ResearcherService $service;

    public function __construct(ResearcherService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $data = $this->service->getAll();
        Response::success($data);
    }

    public function show(int $id): void
    {
        $data = $this->service->getById($id);
        Response::success($data);
    }

    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $this->service->create($input);

        Response::success(['id' => $id], 'Researcher created', 201);
    }

    public function update(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $this->service->update($id, $input);

        Response::success(null, 'Researcher updated');
    }

    public function destroy(int $id): void
    {
        $this->service->delete($id);
        Response::success(null, 'Researcher deleted');
    }
}