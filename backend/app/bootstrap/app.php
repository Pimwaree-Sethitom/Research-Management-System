<?php

use RMS\Backend\Core\Container;
use RMS\Backend\Models\UserManage;
use RMS\Backend\Services\JwtService;
use RMS\Backend\Middlewares\AuthMiddleware;
use RMS\Backend\Middlewares\JwtMiddleware;
use RMS\Backend\Middlewares\RoleMiddleware;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Services\AuthService;
use RMS\Backend\Services\UserManageService;
use RMS\Backend\Services\SeederService;
use RMS\Backend\Controllers\UserController;


$container = new Container();

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
$container->bind(UserManage::class, function () {
    return new UserManage();
});

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/
$container->bind(UserManageService::class, function ($c) {
    return new UserManageService(
        $c->get(UserManage::class)
    );
});

$container->bind(JwtService::class, function () {
    return new JwtService();
});

$container->bind(AuthService::class, function ($c) {
    return new AuthService(
        $c->get(UserManage::class),        
        $c->get(JwtService::class)   
    );
});

$container->bind(SeederService::class, function ($c) {
    return new SeederService(
        $c->get(UserManage::class)
    );
});

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
$container->bind(UserController::class, function ($c) {
    return new UserController(
        $c->get(UserManageService::class)
    );
});

$container->bind(AuthController::class, function ($c) {
    return new AuthController(
        $c->get(AuthService::class)
    );
});

/*
|--------------------------------------------------------------------------
| Middlewares
|--------------------------------------------------------------------------
*/
$container->bind(AuthMiddleware::class, function ($c) {
    return new AuthMiddleware(
        $c->get(JwtService::class)
    );
});

$container->bind(JwtMiddleware::class, function ($c) {
    return new JwtMiddleware(
        $c->get(UserManageService::class)
    );
});

$container->bind(RoleMiddleware::class, function () {
    return new RoleMiddleware(['admin']);
});

// Run Seeders
$container->get(SeederService::class)->seedAdmin();

return $container;