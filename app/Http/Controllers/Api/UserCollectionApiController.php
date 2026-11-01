<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConditionGrade;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollectionResource;
use App\Models\Coin;
use App\Models\UserCollection;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class UserCollectionApiController extends Controller
{
    use ApiResponse;

    /**
     * Kullanıcının şahsi mobil koleksiyon defterini listeler.
     */
    public function index(Request $request): JsonResponse
    {
        $items = $request->user()->collections()
            ->with([
                'coin.period',
                'coin.ruler',
                'coin.primaryImage',
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
     * Koleksiyona yeni sikke ekleme.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coin_id' => ['required', 'integer', 'exists:coins,id'],
            'user_condition' => ['required', new Enum(ConditionGrade::class)],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $item = $request->user()->collections()->create([
            'coin_id' => $validated['coin_id'],
            'user_condition' => $validated['user_condition'],
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $item->load(['coin.primaryImage', 'coin.period', 'coin.ruler']);

        return $this->success(
            new UserCollectionResource($item),
            'Sikke şahsi koleksiyonunuza başarıyla eklendi.',
            201
        );
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

