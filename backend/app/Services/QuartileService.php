<?php

namespace RMS\Backend\Services;

use RMS\Backend\Models\Quartile;

class QuartileService
{
    private Quartile $model;

    public function __construct(Quartile $model)
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

    public function isBeingUsed(int $id): bool
    {
        return $this->model->isBeingUsed($id);
    }
}
