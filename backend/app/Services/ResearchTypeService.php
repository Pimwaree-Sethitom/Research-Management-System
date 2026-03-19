<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\ResearchType;

class ResearchTypeService
{
    private ResearchType $model;

    public function __construct(ResearchType $model)
    {
        $this->model = $model;
    }

    public function getAll(): array
    {
        return $this->model->getAll();
    }

    public function getById(int $id): ?array
    {
        return $this->model->getById($id);
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
