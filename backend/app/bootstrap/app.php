<?php

use RMS\Backend\Core\Container;
use RMS\Backend\Models\UserManage;
use RMS\Backend\Models\ResearchType;
use RMS\Backend\Models\Quartile;
use RMS\Backend\Models\WorkloadDefinition;
use RMS\Backend\Services\ResearchTypeService;
use RMS\Backend\Services\QuartileService;
use RMS\Backend\Services\WorkloadDefinitionService;
use RMS\Backend\Controllers\ResearchTypeController;
use RMS\Backend\Controllers\QuartileController;
use RMS\Backend\Controllers\WorkloadDefinitionController;
use RMS\Backend\Models\ResearchManage;
use RMS\Backend\Services\JwtService;
use RMS\Backend\Middlewares\AuthMiddleware;
use RMS\Backend\Middlewares\JwtMiddleware;
use RMS\Backend\Middlewares\RoleMiddleware;
use RMS\Backend\Controllers\AuthController;
use RMS\Backend\Services\AuthService;
use RMS\Backend\Services\UserManageService;
use RMS\Backend\Services\ResearchService;
use RMS\Backend\Services\SeederService;
use RMS\Backend\Controllers\UserController;
use RMS\Backend\Controllers\ResearchController;
use RMS\Backend\Controllers\HealthController;


$container = new Container();

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
$container->bind(UserManage::class, function () {
    return new UserManage();
});

    $container->bind(ResearchManage::class, fn() => new ResearchManage());
    $container->bind(ResearchType::class, fn() => new ResearchType());
    $container->bind(Quartile::class, fn() => new Quartile());
    $container->bind(WorkloadDefinition::class, fn() => new WorkloadDefinition());

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

    $container->bind(ResearchService::class, fn() => new ResearchService($container->get(ResearchManage::class)));
    $container->bind(ResearchTypeService::class, fn() => new ResearchTypeService($container->get(ResearchType::class)));
    $container->bind(QuartileService::class, fn() => new QuartileService($container->get(Quartile::class)));
    $container->bind(WorkloadDefinitionService::class, fn($c) => new WorkloadDefinitionService($c->get(WorkloadDefinition::class)));

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

    $container->bind(ResearchController::class, fn() => new ResearchController($container->get(ResearchService::class)));
    $container->bind(ResearchTypeController::class, fn() => new ResearchTypeController($container->get(ResearchTypeService::class)));
    $container->bind(QuartileController::class, fn() => new QuartileController($container->get(QuartileService::class)));
    $container->bind(WorkloadDefinitionController::class, fn($c) => new WorkloadDefinitionController($c->get(WorkloadDefinitionService::class)));

$container->bind(AuthController::class, function ($c) {
    return new AuthController(
        $c->get(AuthService::class)
    );
});

$container->bind(HealthController::class, function () {
    return new HealthController();
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
