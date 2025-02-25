<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(string $message = 'Success', array $data = [], int $statusCode = 200)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $message = 'Error', int $statusCode = 400, array $data = [])
    {
        return response()->json([
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }


    public static function conflict($message, $data = null)
    {
        return response()->json([
            'statusCode' => 409,
            'message'    => $message,
            'data'       => $data,
        ], 409);
    }
}