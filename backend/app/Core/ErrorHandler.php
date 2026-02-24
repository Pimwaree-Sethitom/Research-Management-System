<?php

namespace RMS\Backend\Core;

use RMS\Backend\Utils\Response;
use Throwable;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(Throwable $exception): void
    {
        $statusCode = $exception->getCode();

        if (!$statusCode || $statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        Response::error(
            $exception->getMessage(),
            $statusCode
        );
    }
}