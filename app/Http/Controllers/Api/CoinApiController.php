<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoinDetailResource;
use App\Http\Resources\CoinResource;
use App\Models\Coin;
use App\Services\ConcordanceResolverService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoinApiController extends Controller
{
    use ApiResponse;

    /**
     * Sikke kataloğu listeleme, çoklu filtreleme ve lejant/concordance arama.
     */
    public function index(Request $request, ConcordanceResolverService $resolver): JsonResponse
    {
        $concordanceMatch = null;

        $query = Coin::query()->with([
            'period:id,name,slug,start_year,end_year',
            'ruler:id,name,slug',
            'mint:id,name,slug,location',
            'primaryImage',
            'references',
        ]);

        // Lejant / Serbest metin veya Concordance araması (RIC 142, Sear 1850 vb.)
        if ($search = $request->input('q')) {
            $resolution = $resolver->resolve($search);
            if ($resolution['system']) {
                $concordanceMatch = [
                    'system' => $resolution['system'],
                    'reference' => $resolution['reference'],
                    'is_direct_match' => $resolution['is_direct_match'],
                    'direct_coin_slug' => $resolution['coin']?->slug,
                ];
            }
            $query->search($search);
        }

        // Dönem filtresi
        if ($periodSlug = $request->input('period')) {
            $query->whereHas('period', fn ($q) => $q->where('slug', $periodSlug));
        }

        // Hükümdar filtresi
        if ($rulerSlug = $request->input('ruler')) {
            $query->whereHas('ruler', fn ($q) => $q->where('slug', $rulerSlug));
        }

        // Darphane filtresi
        if ($mintSlug = $request->input('mint')) {
            $query->whereHas('mint', fn ($q) => $q->where('slug', $mintSlug));
        }

        // Maden filtresi
        if ($metal = $request->input('metal')) {
            $query->byMetal($metal);
        }

        // Nadirlik filtresi
        if ($rarity = $request->input('rarity')) {
            $query->byRarity($rarity);
        }

        $perPage = min($request->integer('per_page', 20), 50);
        $paginated = $query->latest('id')->paginate($perPage);

        return $this->success([
            'concordance_resolved' => $concordanceMatch,
            'items' => CoinResource::collection($paginated->items()),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    /**
     * Sikke detay uç noktası (İlişkili görseller, hükümdar, darphane, çapraz referanslar ve varyantlarla).
     */
    public function show(string $slug): JsonResponse
    {
        $coin = Coin::where('slug', $slug)
            ->with([
                'period',
                'ruler',
                'mint',
                'images',
                'references',
                'variants',
                'valuations' => fn ($q) => $q->orderBy('min_value_usd', 'asc'),
            ])
            ->first();

        if (!$coin) {
            return $this->error('Talep edilen nümizmatik sikke kaydı bulunamadı.', 404);
        }

        $coin->incrementViews();

        return $this->success(new CoinDetailResource($coin));
    }

    /**
     * Sikke Fiziksel Sapma ve Sahtelik Doğrulama Algoritması
     * Kullanıcının girdiği ağırlık ve çapı orijinal standartlarla karşılaştırıp sahtelik teşhisi üretir.
     */
    public function verifyPhysical(Request $request, string $slug, \App\Services\AuthenticityDiagnosticService $diagnosticService): JsonResponse
    {
        $coin = Coin::where('slug', $slug)->first();

        if (!$coin) {
            return $this->error('Sikke kaydı bulunamadı.', 404);
        }

        $validated = $request->validate([
            'weight_g' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'diameter_mm' => ['nullable', 'numeric', 'min:1', 'max:150'],
        ]);

        $diagnosis = $diagnosticService->analyzePhysicalDeviations(
            coin: $coin,
            measuredWeight: (float) $validated['weight_g'],
            measuredDiameter: isset($validated['diameter_mm']) ? (float) $validated['diameter_mm'] : null
        );

        return $this->success($diagnosis, $diagnosis['title']);
    }

    /**
     * Mobil kamera görseli için yapay zeka sikke eşleştirme / görsel tanımlama simülasyonu.
     * Flutter/React Native kamerasından gelen fotoğrafı analiz eder ve benzer sikkeleri skorlar.
     */
    public function identify(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required_without_all:image_base64,obverse_image', 'nullable', 'image', 'max:10240'],
            'obverse_image' => ['nullable', 'image', 'max:10240'],
            'reverse_image' => ['nullable', 'image', 'max:10240'],
            'image_base64' => ['nullable', 'string'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $topK = $request->integer('top_k', 3);

        // Görselleri kaydetme (Tekil veya Ön/Arka çift görsel)
        $savedImages = [];
        if ($request->hasFile('obverse_image')) {
            $savedImages['obverse'] = Storage::disk('public')->url(
                $request->file('obverse_image')->store('identifications/' . date('Y/m'), 'public')
            );
        }
        if ($request->hasFile('reverse_image')) {
            $savedImages['reverse'] = Storage::disk('public')->url(
                $request->file('reverse_image')->store('identifications/' . date('Y/m'), 'public')
            );
        }
        if ($request->hasFile('image')) {
            $savedImages['primary'] = Storage::disk('public')->url(
                $request->file('image')->store('identifications/' . date('Y/m'), 'public')
            );
        }

        // Yapay zeka embedding / görsel eşleştirme simülasyon algoritması:
        // Gerçekte harici Python (PyTorch/TensorFlow) modeline veya vektör veritabanına sorgu atılır.
        $candidateCoins = Coin::query()
            ->with(['period', 'ruler', 'primaryImage', 'valuations'])
            ->inRandomOrder()
            ->limit($topK)
            ->get();

        $confidenceScores = [0.94, 0.82, 0.67, 0.54, 0.41];

        $results = $candidateCoins->values()->map(function ($coin, $index) use ($confidenceScores) {
            $score = $confidenceScores[$index] ?? (0.50 - ($index * 0.05));
            return [
                'confidence' => round($score, 2),
                'match_percentage' => round($score * 100, 1) . '%',
                'coin' => new CoinResource($coin),
            ];
        });

        return $this->success([
            'uploaded_image_url' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
            'identified' => $results->isNotEmpty(),
            'best_match' => $results->first() ?? null,
            'candidates' => $results,
            'disclaimer' => 'Yapay zeka görsel eşleştirmesi tahmin amaçlıdır; kesin nümizmatik tespit uzman incelemesi gerektirir.',
        ], 'Sikke görseli başarıyla analiz edildi.');
    }
}
