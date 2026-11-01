<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RulerResource;
use App\Models\Ruler;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RulerApiController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $rulers = Ruler::with(['period'])
            ->withCount('coins')
            ->orderBy('reign_start')
            ->get();

        return $this->success(RulerResource::collection($rulers));
    }

    public function show(string $slug): JsonResponse
    {
        $ruler = Ruler::where('slug', $slug)
            ->with(['period'])
            ->withCount('coins')
            ->first();

        if (!$ruler) {
            return $this->error('Hükümdar bulunamadı.', 404);
        }

        return $this->success(new RulerResource($ruler));
    }
}

