<?php

namespace RMS\Backend\Utils;

class Response
{
    public static function success($data = null, string $message = null, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
        exit;
    }

    public static function error(string $message, int $status = 400, $errors = null): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }
}
