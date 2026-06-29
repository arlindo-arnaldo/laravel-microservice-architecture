<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{


    public function success(mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], $code);
    }

    public function error(string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message
        ], $code);
    }
}
