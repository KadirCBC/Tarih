<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeriodResource;
use App\Models\Period;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PeriodApiController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $periods = Period::withCount(['coins', 'rulers'])
            ->orderBy('start_year')
            ->get();

        return $this->success(PeriodResource::collection($periods));
    }

    public function show(string $slug): JsonResponse
    {
        $period = Period::where('slug', $slug)
            ->withCount(['coins', 'rulers'])
            ->first();

        if (!$period) {
            return $this->error('Dönem bulunamadı.', 404);
        }

        return $this->success(new PeriodResource($period));
    }
}

