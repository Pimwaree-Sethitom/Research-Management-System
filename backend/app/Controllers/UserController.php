<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Services\UserManageService;
use RMS\Backend\Utils\Response;

class UserController
{
    private UserManageService $service;

    public function __construct(UserManageService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $data = $this->service->getAll();
        Response::success($data);
    }
}
