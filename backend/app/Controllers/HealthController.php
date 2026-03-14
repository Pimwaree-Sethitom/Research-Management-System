<?php

namespace RMS\Backend\Controllers;

use RMS\Backend\Utils\Response;

class HealthController
{
    public static function index(): void
    {
        Response::success([
            'welcome' => 'ICT Research Management System API',
            'version' => '1.0.0',
            'links' => [
                'health' => '/health',
                'docs' => '/docs/swagger.html'
            ]
        ]);
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

