<?php

use RMS\Backend\Controllers\HealthController;
use RMS\Backend\Controllers\ResearcherController;

$router->get('/health', [HealthController::class, 'check']);

$router->get('/researchers', [ResearcherController::class, 'index']);
$router->get('/researchers/{id}', [ResearcherController::class, 'show']);
$router->post('/researchers', [ResearcherController::class, 'store']);
$router->put('/researchers/{id}', [ResearcherController::class, 'update']);
$router->delete('/researchers/{id}', [ResearcherController::class, 'destroy']);