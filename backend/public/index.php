<?php

require __DIR__ . '/../vendor/autoload.php';

use RMS\Backend\Core\Router;
use RMS\Backend\Core\ErrorHandler;
use RMS\Backend\Core\Container;
use RMS\Backend\Models\Researcher;
use RMS\Backend\Services\ResearcherService;
use RMS\Backend\Controllers\ResearcherController;

$container = new Container();

$container->bind(Researcher::class, function() {
    return new Researcher();
});

$container->bind(ResearcherService::class, function($c) {
    return new ResearcherService(
        $c->get(Researcher::class)
    );
});

$container->bind(ResearcherController::class, function($c) {
    return new ResearcherController(
        $c->get(ResearcherService::class)
    );
});

ErrorHandler::register();

$router = new Router($container);

require __DIR__ . '/../routes/api.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);