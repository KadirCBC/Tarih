<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Recognition\LlmCoinRecognitionService;
use App\Services\Recognition\VectorCoinRecognitionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoinRecognitionController extends Controller
{
    use ApiResponse;

    protected LlmCoinRecognitionService $llmService;
    protected VectorCoinRecognitionService $vectorService;

    public function __construct(
        LlmCoinRecognitionService $llmService,
        VectorCoinRecognitionService $vectorService
    ) {
        $this->llmService = $llmService;
        $this->vectorService = $vectorService;
    }

    /**
     * Sikke Tanıma API Uç Noktası
     *
     * @param Request $request
     * - obverse_image: Dosya (Zorunlu)
     * - reverse_image: Dosya (Zorunlu)
     * - method: 'llm' (Önerilen / Yöntem A) veya 'vector' (Yöntem B)
     */
    public function recognize(Request $request): JsonResponse
    {
        $request->validate([
            'obverse_image' => ['required', 'image', 'max:10240'],
            'reverse_image' => ['required', 'image', 'max:10240'],
            'method' => ['nullable', 'string', 'in:llm,vector,hybrid'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $obverse = $request->file('obverse_image');
        $reverse = $request->file('reverse_image');
        $method = $request->input('method', 'llm');
        $topK = $request->integer('top_k', 5);

        // Yöntem Seçimi
        if ($method === 'vector') {
            // Yöntem B: CLIP Vektör Benzerliği (Kosinüs Karşılaştırması)
            $result = $this->vectorService->identify($obverse, $reverse, $topK);
        } else {
            // Yöntem A: Gemini Flash Multimodal LLM (Epigrafi & Lejant Analizli)
            $result = $this->llmService->identify($obverse, $reverse);
        }

        return $this->success($result, 'Sikke analizi ve veritabanı eşleştirmesi tamamlandı.');
    }
}

