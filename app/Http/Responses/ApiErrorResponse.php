<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class ApiErrorResponse
{
    public static function create(\Exception $exception, int $statusCode = 500, $metadata = []): JsonResponse
    {
        $defaultMetadata = [
            'status_code' => $statusCode,
            'timestamp' => now()->toDateTimeString(),
        ];

        $response = [
            'status' => 'error',
            'metadata' => $defaultMetadata,
        ];

        if ($exception instanceof ValidationException) {
            $response['errors'] = $exception->errors();
        } else {
            $response['error'] = 'An error occurred';
            if (Config::get('app.debug')) {
                $response['message'] = $exception->getMessage();
            }
        }

        return response()->json($response, $statusCode);
    }
}