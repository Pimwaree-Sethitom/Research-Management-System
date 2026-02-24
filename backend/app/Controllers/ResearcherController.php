<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Models\Researcher;
use RMS\Backend\Utils\Response;

class ResearcherController
{
    /* ---------- GET /researchers ---------- */
    public function index(): void
    {
        $model = new Researcher();
        $data = $model->getAll();

        Response::success($data);
    }

    /* ---------- GET /researchers/{id} ---------- */
    public function show(int $id): void
    {
        $model = new Researcher();
        $data = $model->getById($id);

        if (!$data) {
            Response::error('Researcher not found', 404);
            return;
        }

        Response::success($data);
    }

    /* ---------- POST /researchers ---------- */
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (
            empty($input['name']) ||
            empty($input['name_department_eng']) ||
            empty($input['name_department_thai'])
        ) {
            Response::error('Invalid input', 422);
            return;
        }

        $model = new Researcher();
        $id = $model->create($input);

        Response::success([
            'id' => $id
        ], 'Researcher created', 201);
    }

    /* ---------- PUT /researchers/{id} ---------- */
    public function update(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $model = new Researcher();

        if (!$model->getById($id)) {
            Response::error('Researcher not found', 404);
            return;
        }

        $model->update($id, $input);

        Response::success(null, 'Researcher updated');
    }

    /* ---------- DELETE /researchers/{id} ---------- */
    public function destroy(int $id): void
    {
        $model = new Researcher();

        if (!$model->getById($id)) {
            Response::error('Researcher not found', 404);
            return;
        }

        $model->delete($id);

        Response::success(null, 'Researcher deleted');
    }
}