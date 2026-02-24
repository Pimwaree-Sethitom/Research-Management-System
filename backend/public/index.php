<?php

require __DIR__ . '/../vendor/autoload.php';

use RMS\Backend\Core\Router;
use RMS\Backend\Core\ErrorHandler;

ErrorHandler::register();

$router = new Router();

require __DIR__ . '/../routes/api.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);