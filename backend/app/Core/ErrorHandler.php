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
        $code = $exception->getCode();

        // PDOException อาจส่งค่า code เป็น string (เช่น '42S22') 
        // เราต้องตรวจสอบและบังคับให้เป็น int สำหรับ HTTP Status
        if (!is_int($code) || $code < 100 || $code >= 600) {
            $statusCode = 500;
        } else {
            $statusCode = $code;
        }

        Response::error(
            $exception->getMessage(),
            $statusCode
        );
    }
}