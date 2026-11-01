<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RegnalDateConverterService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegnalCalculatorApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected RegnalDateConverterService $converter
    ) {}

    /**
     * Takvim ve Regnal Yıl Hesaplama API Uç Noktası.
     * POST /api/v1/dating/convert
     */
    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'system' => 'required|string|in:roman,seleucid,islamic,roma,grek,osmanli',
            'ruler' => 'nullable|string',
            'tr_p' => 'nullable',
            'cos' => 'nullable',
            'raw_titles' => 'nullable|string',
            'as_year' => 'nullable',
            'year' => 'nullable',
            'accession_hijri' => 'nullable|integer',
            'regnal_year' => 'nullable|integer',
            'sultan' => 'nullable|string',
        ]);

        $result = $this->converter->calculate($validated);

        if (!($result['success'] ?? false)) {
            return $this->error($result['error'] ?? 'Hesaplama başarısız oldu.', 422);
        }

        return $this->success($result);
    }

    /**
     * Formlar için referans imparator ve sultan listesi.
     * GET /api/v1/dating/reference-data
     */
    public function referenceData(): JsonResponse
    {
        return $this->success([
            'roman_emperors' => $this->converter->getSupportedRomanEmperors(),
            'ottoman_sultans' => $this->converter->getSupportedOttomanSultans(),
            'seleucid_era' => [
                'start_year_bc' => 312,
                'description' => 'M.Ö. 312 Sonbaharı (Dios ayı / I. Seleukos Nikator Babil girişi)',
            ],
        ]);
    }
}

