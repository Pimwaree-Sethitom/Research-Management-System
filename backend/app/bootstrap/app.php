<?php

use RMS\Backend\Core\Container;
use RMS\Backend\Models\Researcher;
use RMS\Backend\Models\User;
use RMS\Backend\Services\ResearcherService;
use RMS\Backend\Controllers\ResearcherController;
use RMS\Backend\Services\JwtService;
use RMS\Backend\Middlewares\AuthMiddleware;
use RMS\Backend\Middlewares\JwtMiddleware;
use RMS\Backend\Middlewares\RoleMiddleware;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Services\AuthService;

$container = new Container();

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
$container->bind(Researcher::class, function () {
    return new Researcher();
});

$container->bind(User::class, function () {
    return new User();
});

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/
$container->bind(ResearcherService::class, function ($c) {
    return new ResearcherService(
        $c->get(Researcher::class)
    );
});

$container->bind(JwtService::class, function () {
    return new JwtService();
});

$container->bind(AuthService::class, function ($c) {
    return new AuthService(
        $c->get(User::class),        
        $c->get(JwtService::class)   
    );
});

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
$container->bind(ResearcherController::class, function ($c) {
    return new ResearcherController(
        $c->get(ResearcherService::class)
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
        $c->get(JwtService::class)
    );
});

$container->bind(RoleMiddleware::class, function () {
    return new RoleMiddleware(['admin']);
});

return $container;