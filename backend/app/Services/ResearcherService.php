<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\Researcher;
use Exception;

class ResearcherService
{
    private Researcher $model;

    public function __construct()
    {
        $this->model = new Researcher();
    }

    public function getAll(): array
    {
        return $this->model->getAll();
    }

    public function getById(int $id): array
    {
        $data = $this->model->getById($id);

        if (!$data) {
            throw new Exception("Researcher not found", 404);
        }

        return $data;
    }

    public function create(array $input): int
    {
        if (
            empty($input['name']) ||
            empty($input['name_department_eng']) ||
            empty($input['name_department_thai'])
        ) {
            throw new Exception("Invalid input", 422);
        }

        return $this->model->create($input);
    }

    public function update(int $id, array $input): void
    {
        if (!$this->model->getById($id)) {
            throw new Exception("Researcher not found", 404);
        }

        $this->model->update($id, $input);
    }

    public function delete(int $id): void
    {
        if (!$this->model->getById($id)) {
            throw new Exception("Researcher not found", 404);
        }

        $this->model->delete($id);
    }
}