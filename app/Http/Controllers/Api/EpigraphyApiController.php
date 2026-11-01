<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Epigraphy\CircularTextUnwrapperService;
use App\Services\Epigraphy\LegendReconstructionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpigraphyApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected LegendReconstructionService $reconstructionService,
        protected CircularTextUnwrapperService $unwrapperService
    ) {}

    /**
     * Kullanıcının girdiği kısmi harf/wildcard metnini tamamlar.
     * Örn: "...NERV...TR...PARTH..."
     */
    public function reconstructFromText(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:150'],
            'face' => ['nullable', 'string', 'in:obverse,reverse,both'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:15'],
        ]);

        $query = $validated['query'];
        $face = $validated['face'] ?? 'both';
        $limit = (int) ($validated['limit'] ?? 5);

        $matches = $this->reconstructionService->reconstruct($query, $face, $limit);

        if (empty($matches)) {
            return $this->error('Girdiğiniz harf öbeğiyle eşleşen standart bir epigrafik lejant bulunamadı.', 404, [
                'input' => $query,
            ]);
        }

        $topMatch = $matches[0];

        return $this->success([
            'input' => $query,
            'primary_feedback' => $topMatch['formatted_feedback'],
            'confidence_score' => $topMatch['confidence_score'],
            'best_match' => $topMatch,
            'candidates' => $matches,
        ], 'Epigrafik lejant başarıyla tamamlandı.');
    }

    /**
     * Fotoğraftaki silik/dairesel yazıyı polar transform ile düzleştirir,
     * OCR ve epigrafik rekonstrüksiyon yaparak sonuç döner.
     */
    public function reconstructFromImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,jpg', 'max:12288'], // Maks 12MB
            'face' => ['nullable', 'string', 'in:obverse,reverse,both'],
        ]);

        $file = $request->file('image');
        $face = $validated['face'] ?? 'obverse';

        $result = $this->unwrapperService->unwrapAndReconstruct($file, $face);

        return $this->success([
            'detected_raw_text' => $result['detected_raw_text'],
            'epigraphy_language' => $result['epigraphy_language'],
            'unwrapped_strip_url' => $result['unwrapped_strip_url'],
            'formatted_feedback' => $result['formatted_feedback'],
            'confidence_score' => $result['confidence_score'],
            'top_match' => $result['top_match'],
            'candidates' => $result['all_candidates'],
        ], 'Sikke görselindeki epigrafik yazı başarıyla düzleştirilip tamamlandı.');
    }
}

