<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiSuccessResponse
{
    public static function create($data = null, $message = 'Success', $statusCode = 200, $metadata = []): JsonResponse
    {
        $defaultMetadata = [
            'status_code' => $statusCode,
            'timestamp' => now()->toDateTimeString(),
        ];

        if ($data instanceof LengthAwarePaginator) {
            $paginationDetails = [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ];

            $metadata = array_merge($metadata, ['pagination' => $paginationDetails]);
            $data = $data->items();
        }

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