<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiSuccessResponse
{
    public static function create($data = null, $message = 'Success', $statusCode = 200, $metadata = []): JsonResponse
    {
        $defaultMetadata = [
            'status_code' => $statusCode,
            'timestamp' => now()->toDateTimeString(),
        ];

        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'metadata' => array_merge($defaultMetadata, $metadata),
        ];

        if (is_null($message)) {
            unset($response['message']);
        }

        if (is_null($data)) {
            unset($response['data']);
        }

        return response()->json($response, $statusCode);
    }
}