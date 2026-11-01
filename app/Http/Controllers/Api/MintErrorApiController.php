<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MintErrorType;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MintErrorApiController extends Controller
{
    use ApiResponse;

    /**
     * Darp Hatası Türleri ve Prim Çarpanları API Listesi.
     * GET /api/v1/mint-errors
     */
    public function index(): JsonResponse
    {
        $types = MintErrorType::all();

        return $this->success($types->map(function ($error) {
            return [
                'id' => $error->id,
                'name' => $error->name,
                'slug' => $error->slug,
                'severity_impact' => $error->severity_impact,
                'multiplier_min' => (float) $error->multiplier_min,
                'multiplier_max' => (float) $error->multiplier_max,
                'visual_guide_image' => $error->visual_guide_image,
                'explanation_tr' => $error->explanation_tr,
            ];
        }));
    }
}

