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

    /**
     * Coğrafi koordinatları bulunan tüm darphaneleri ve en popüler 5 sikkesini
     * RFC 7946 uyumlu GeoJSON FeatureCollection formatında döner.
     */
    public function geoJson(): JsonResponse
    {
        $mints = Mint::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('coins')
            ->with([
                'coins' => function ($query) {
                    $query->orderByDesc('view_count')
                        ->limit(5)
                        ->with('primaryImage', 'period:id,name', 'ruler:id,name');
                },
            ])
            ->get();

        $features = $mints->map(function (Mint $mint) {
            $topCoins = $mint->coins->map(function ($coin) {
                return [
                    'id' => $coin->id,
                    'title' => $coin->title,
                    'slug' => $coin->slug,
                    'denomination' => $coin->denomination,
                    'metal' => $coin->metal,
                    'rarity_grade' => $coin->rarity_grade?->value ?? $coin->rarity_grade,
                    'view_count' => $coin->view_count,
                    'thumbnail_url' => $coin->primaryImage?->image_url ?? '/images/coin-placeholder.webp',
                    'detail_url' => route('coins.show', $coin->slug),
                    'period' => $coin->period?->name,
                    'ruler' => $coin->ruler?->name,
                ];
            });

            return [
                'type' => 'Feature',
                'id' => $mint->id,
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float) $mint->longitude,
                        (float) $mint->latitude,
                    ],
                ],
                'properties' => [
                    'id' => $mint->id,
                    'name' => $mint->name,
                    'slug' => $mint->slug,
                    'ancient_name' => $mint->ancient_name ?: $mint->name,
                    'modern_name' => $mint->modern_name ?: $mint->location,
                    'active_period' => $mint->active_period ?: 'Antik Çağ',
                    'historical_significance' => $mint->historical_significance,
                    'coins_count' => $mint->coins_count,
                    'filter_url' => route('coins.index', ['mint' => $mint->slug]),
                    'top_coins' => $topCoins,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}

