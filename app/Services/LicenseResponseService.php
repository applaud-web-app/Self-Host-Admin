<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Http\JsonResponse;

class LicenseResponseService
{
    public function successResponse(License $license): JsonResponse
    {
        return response()->json([
            'valid' => true,
            'message' => 'License activated successfully.',
            'license' => [
                'key' => $license->raw_key,
                'domain' => $license->activated_domain
            ],
            'product' => [
                'slug' => $license->product->slug ?? null,
                'version' => $license->product->version ?? null,
                'name' => $license->product->name ?? null,
            ],
        ]);
    }

    public function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'valid' => false,
            'message' => $message,
        ], $statusCode);
    }
}
