<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Utils\Response;

class HealthController
{
    public static function index(): void
    {
        Response::redirect('/docs/swagger.html');
    }

    public static function check(): void
    {
        Response::success([
            'status' => 'ok',
            'service' => 'research-management-backend',
            'timestamp' => date('c')
        ]);
    }
}

