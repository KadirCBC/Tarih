<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConditionGrade;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollectionResource;
use App\Models\UserCollection;
use App\Services\CollectionPortfolioService;
use App\Services\ImageOptimizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response;

class UserCollectionApiController extends Controller
{
    use ApiResponse;

    protected CollectionPortfolioService $portfolioService;

    public function __construct(CollectionPortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * Kullanıcının şahsi mobil koleksiyon defterini listeler.
     */
    public function index(Request $request): JsonResponse
    {
        $items = $request->user()->collections()
            ->with([
                'coin.period',
                'coin.ruler',
                'coin.mint',
                'coin.primaryImage',
                'coin.valuations',
            ])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->success([
            'items' => UserCollectionResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * Koleksiyona yeni sikke ekleme (Kondisyon, özel fotoğraf ve notlarla).
     */
    public function store(Request $request, ImageOptimizationService $imageService): JsonResponse
    {
        $user = $request->user();

        // Freemium Kota Kontrolü: Free kullanıcı için maksimum 5 sikke
        if (!$user->canAddToCollection()) {
            return response()->json([
                'success' => false,
                'error_code' => 'COLLECTION_LIMIT_REACHED',
                'message' => 'Ücretsiz planda en fazla 5 sikke kaydedebilirsiniz. Nümizmat Pro\'ya geçerek sınırsız koleksiyon oluşturabilir ve PDF sertifikası indirebilirsiniz.',
                'upgrade_required' => true,
                'current_count' => $user->collections()->count(),
                'max_limit' => $user->getMaxCollectionLimit(),
            ], 403);
        }

        $validated = $request->validate([
            'coin_id' => ['required', 'integer', 'exists:coins,id'],
            'user_condition' => ['required', new Enum(ConditionGrade::class)],
            'has_mint_error' => ['nullable', 'boolean'],
            'mint_error_type_id' => ['nullable', 'exists:mint_error_types,id'],
            'mint_error_notes' => ['nullable', 'string', 'max:500'],
            'purchase_price_usd' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000', new \App\Rules\NoIllegalTradingRule],
            'custom_image' => ['nullable', 'image', 'max:10240'], // Kullanıcının sikke fotoğrafı
            'legal_declaration' => ['required_with:custom_image', 'accepted'],
        ], [
            'legal_declaration.accepted' => 'Bu görselin yasal haklarına sahip olduğunuzu onaylamanız gerekmektedir.',
        ]);

        $customImagePath = null;
        if ($request->hasFile('custom_image')) {
            $customImagePath = $imageService->optimizeAndStore(
                file: $request->file('custom_image'),
                folder: 'collections/' . $request->user()->id,
                maxWidth: 1600,
                quality: 85
            );
        }

        $item = $request->user()->collections()->create([
            'coin_id' => $validated['coin_id'],
            'user_condition' => $validated['user_condition'],
            'has_mint_error' => $request->boolean('has_mint_error'),
            'mint_error_type_id' => $validated['mint_error_type_id'] ?? null,
            'mint_error_notes' => $validated['mint_error_notes'] ?? null,
            'custom_image_path' => $customImagePath,
            'purchase_price_usd' => $validated['purchase_price_usd'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $item->load(['coin.primaryImage', 'coin.period', 'coin.ruler']);

        return $this->success(
            new UserCollectionResource($item),
            'Sikke şahsi envanterinize başarıyla kaydedildi.',
            201
        );
    }

    /**
     * Kullanıcının toplam portföy değerini ve özet analizini döndürür.
     * Örn: "Portföyünüzde 14 sikke var, tahmini toplam değer: $3,200 - $4,800 USD"
     */
    public function portfolioSummary(Request $request): JsonResponse
    {
        $summary = $this->portfolioService->getPortfolioSummary($request->user());

        return $this->success($summary, $summary['summary_message']);
    }

    /**
     * Nümizmatik Koleksiyon Sertifikası PDF indirme.
     */
    public function exportPdf(Request $request): Response
    {
        $pdf = $this->portfolioService->generateInventoryPdf($request->user());

        $fileName = 'Numizmatik_Envanter_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * İki Yönlü (Bidirectional) Offline-First Senkronizasyon Uç Noktası.
     * Mobil cihaz internete bağlandığında yerel SQLite'taki değişiklikleri sunucuyla eşitler
     * ve sunucudaki yeni güncellemeleri istemciye gönderir.
     */
    public function sync(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'last_synced_at' => ['nullable', 'date'],
            'created' => ['nullable', 'array'],
            'created.*.local_id' => ['required', 'string'],
            'created.*.coin_id' => ['required', 'exists:coins,id'],
            'created.*.user_condition' => ['required', new Enum(ConditionGrade::class)],
            'created.*.purchase_date' => ['nullable', 'date'],
            'created.*.notes' => ['nullable', 'string', 'max:1000', new \App\Rules\NoIllegalTradingRule()],
            'updated' => ['nullable', 'array'],
            'updated.*.server_id' => ['required', 'integer'],
            'updated.*.user_condition' => ['nullable', new Enum(ConditionGrade::class)],
            'updated.*.purchase_date' => ['nullable', 'date'],
            'updated.*.notes' => ['nullable', 'string', 'max:1000', new \App\Rules\NoIllegalTradingRule()],
            'deleted' => ['nullable', 'array'],
            'deleted.*' => ['integer'],
        ]);

        $createdMapping = [];
        $lastSyncedAt = $validated['last_synced_at'] ? \Illuminate\Support\Carbon::parse($validated['last_synced_at']) : null;

        // 1. İstemcide silinenleri sunucudan sil
        if (!empty($validated['deleted'])) {
            $user->collections()->whereIn('id', $validated['deleted'])->delete();
        }

        // 2. İstemcide güncellenenleri sunucuda güncelle
        if (!empty($validated['updated'])) {
            foreach ($validated['updated'] as $updateItem) {
                $collection = $user->collections()->where('id', $updateItem['server_id'])->first();
                if ($collection) {
                    $collection->update(array_filter([
                        'user_condition' => $updateItem['user_condition'] ?? null,
                        'purchase_date' => $updateItem['purchase_date'] ?? null,
                        'notes' => $updateItem['notes'] ?? null,
                    ]));
                }
            }
        }

        // 3. İstemcide çevrimdışı oluşturulanları sunucuya kaydet
        if (!empty($validated['created'])) {
            foreach ($validated['created'] as $createItem) {
                if (!$user->canAddToCollection()) {
                    continue; // Kota aşıldıysa atla
                }

                $record = $user->collections()->create([
                    'coin_id' => $createItem['coin_id'],
                    'user_condition' => $createItem['user_condition'],
                    'purchase_date' => $createItem['purchase_date'] ?? now()->toDateString(),
                    'notes' => $createItem['notes'] ?? null,
                ]);

                $createdMapping[$createItem['local_id']] = $record->id;
            }
        }

        // 4. Sunucuda son senkronizasyondan sonra değişenleri getir
        $serverChangesQuery = $user->collections()->with([
            'coin.period',
            'coin.ruler',
            'coin.mint',
            'coin.primaryImage',
            'coin.valuations',
        ]);

        if ($lastSyncedAt) {
            $serverChangesQuery->where('updated_at', '>', $lastSyncedAt);
        }

        $serverChanges = $serverChangesQuery->get();

        return $this->success([
            'synced_at' => now()->toIso8601String(),
            'created_mapping' => $createdMapping,
            'server_changes' => UserCollectionResource::collection($serverChanges),
        ], 'Çevrimdışı envanter başarıyla senkronize edildi.');
    }

    /**
     * Koleksiyondan sikke kaydı silme.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $item = $request->user()->collections()->where('id', $id)->first();

        if (!$item) {
            return $this->error('Koleksiyon kaydı bulunamadı veya bu işlem için yetkiniz yok.', 404);
        }

        $item->delete();

        return $this->success(null, 'Sikke koleksiyonunuzdan silindi.');
    }
}
