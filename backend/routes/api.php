<?php

use RMS\Backend\Controllers\HealthController;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Controllers\UserController;
use RMS\Backend\Controllers\ResearchController;
use RMS\Backend\Middlewares\AuthMiddleware;


$router->get('/', [HealthController::class, 'index']);
$router->get('/health', [HealthController::class, 'check']);

$router->post('/login', [AuthController::class, 'login']);


$router->group(['middleware' => [AuthMiddleware::class]], function ($router) {

    // User Management
    $router->get('/usermanage', [UserController::class, 'index']);
    $router->get('/usermanage/{id}', [UserController::class, 'show']);
    $router->post('/usermanage', [UserController::class, 'store']);
    $router->put('/usermanage/{id}', [UserController::class, 'update']);
    $router->delete('/usermanage/{id}', [UserController::class, 'destroy']);
    // Research Management
    $router->get('/research', [ResearchController::class, 'index']);
    $router->post('/research', [ResearchController::class, 'store']);
});