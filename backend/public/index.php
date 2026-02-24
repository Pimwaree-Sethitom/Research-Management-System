<?php

require __DIR__ . '/../vendor/autoload.php';

use RMS\Backend\Core\Router;
use RMS\Backend\Utils\Response;

$router = new Router();

require __DIR__ . '/../routes/api.php';

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    Response::error('Internal Server Error', 500, [
        'exception' => $e->getMessage()
    ]);
}