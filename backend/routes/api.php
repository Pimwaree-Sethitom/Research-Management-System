<?php

use RMS\Backend\Controllers\HealthController;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Controllers\ResearcherController;
use RMS\Backend\Middlewares\AuthMiddleware;


$router->get('/health', [HealthController::class, 'check']);

$router->post('/login', [AuthController::class, 'login']);


$router->group(['middleware' => [AuthMiddleware::class]], function ($router) {

    // Researchers CRUD
    $router->get('/researchers', [ResearcherController::class, 'index']);
    $router->get('/researchers/{id}', [ResearcherController::class, 'show']);
    $router->post('/researchers', [ResearcherController::class, 'store']);
    $router->put('/researchers/{id}', [ResearcherController::class, 'update']);
    $router->delete('/researchers/{id}', [ResearcherController::class, 'destroy']);
});