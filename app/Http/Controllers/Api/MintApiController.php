<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MintResource;
use App\Models\Mint;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MintApiController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $mints = Mint::withCount('coins')
            ->orderBy('name')
            ->get();

        return $this->success(MintResource::collection($mints));
    }

    public function show(string $slug): JsonResponse
    {
        $mint = Mint::where('slug', $slug)
            ->withCount('coins')
            ->first();

        if (!$mint) {
            return $this->error('Darphane bulunamadı.', 404);
        }

        return $this->success(new MintResource($mint));
    }
}

