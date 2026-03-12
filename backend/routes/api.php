<?php

use RMS\Backend\Controllers\HealthController;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Controllers\UserController;
use RMS\Backend\Middlewares\AuthMiddleware;


$router->get('/health', [HealthController::class, 'check']);

$router->post('/login', [AuthController::class, 'login']);


$router->group(['middleware' => [AuthMiddleware::class]], function ($router) {

    // User Management
    $router->get('/usermanage', [UserController::class, 'index']);
});