<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Standart Başarılı JSON Yanıtı
     * {"success": true, "data": {...}, "message": null}
     */
    protected function success(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * Standart Hatalı JSON Yanıtı
     * {"success": false, "data": null, "message": "Hata açıklaması"}
     */
    protected function error(string $message, int $code = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}

