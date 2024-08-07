<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function successResponse($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'status' => 1,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    protected function errorResponse($message, $statusCode = 400)
    {
        return response()->json([
            'status' => 0,
            'error' => $message,
        ], $statusCode);
    }
}
