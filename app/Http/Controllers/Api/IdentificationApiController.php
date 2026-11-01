<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdentificationComment;
use App\Models\IdentificationRequest;
use App\Rules\NoIllegalTradingRule;
use App\Services\ImageOptimizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdentificationApiController extends Controller
{
    use ApiResponse;

    /**
     * Kimlikleme kulübü taleplerini listeleme.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->input('status', 'all');

        $query = IdentificationRequest::with([
            'user:id,name,is_expert,expert_title',
            'identifiedCoin:id,title,slug,catalog_reference',
            'verifiedBy:id,name,is_expert,expert_title',
        ])->withCount('comments');

        if ($status !== 'all' && in_array($status, ['pending', 'identified', 'disputed', 'rejected'])) {
            $query->where('status', $status);
        }

        $paginated = $query->latest('id')->paginate(15);

        return $this->success([
            'items' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Tekil talep detayını yorumları ve uzman kararıyla getirir.
     */
    public function show(int $id): JsonResponse
    {
        $identification = IdentificationRequest::with([
            'user:id,name,is_expert,expert_title',
            'identifiedCoin',
            'verifiedBy:id,name,is_expert,expert_title',
            'comments.user:id,name,is_expert,expert_title',
            'comments.suggestedCoin:id,title,slug,catalog_reference',
        ])->find($id);

        if (!$identification) {
            return $this->error('Kimlikleme talebi bulunamadı.', 404);
        }

        return $this->success($identification);
    }

    /**
     * Yeni kimlikleme talebi açma (Mobil uygulama API).
     */
    public function store(Request $request, ImageOptimizationService $optimizer): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:150', new NoIllegalTradingRule()],
            'description' => ['nullable', 'string', 'max:1000', new NoIllegalTradingRule()],
            'obverse_image' => ['required', 'image', 'max:12288'],
            'reverse_image' => ['nullable', 'image', 'max:12288'],
            'weight_g' => ['nullable', 'numeric', 'min:0.1', 'max:500'],
            'diameter_mm' => ['nullable', 'numeric', 'min:1', 'max:150'],
        ]);

        $obversePath = $optimizer->optimizeToWebp(
            $request->file('obverse_image'),
            'identifications',
            1400,
            82
        );

        $reversePath = null;
        if ($request->hasFile('reverse_image')) {
            $reversePath = $optimizer->optimizeToWebp(
                $request->file('reverse_image'),
                'identifications',
                1400,
                82
            );
        }

        $identification = IdentificationRequest::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'obverse_image' => $obversePath,
            'reverse_image' => $reversePath,
            'weight_g' => $validated['weight_g'] ?? null,
            'diameter_mm' => $validated['diameter_mm'] ?? null,
            'status' => 'pending',
        ]);

        return $this->success($identification, 'Kimlikleme talebiniz uzman kulübüne iletildi.', 201);
    }

    /**
     * Topluluk önerisi / yorumu ekleme.
     */
    public function addComment(Request $request, int $id): JsonResponse
    {
        $identification = IdentificationRequest::find($id);
        if (!$identification) {
            return $this->error('Talep bulunamadı.', 404);
        }

        $user = $request->user();

        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:3', 'max:1500', new NoIllegalTradingRule()],
            'suggested_coin_id' => ['nullable', 'exists:coins,id'],
        ]);

        $comment = IdentificationComment::create([
            'identification_request_id' => $identification->id,
            'user_id' => $user->id,
            'suggested_coin_id' => $validated['suggested_coin_id'] ?? null,
            'comment' => $validated['comment'],
            'is_expert_verification' => $user->isExpert(),
        ]);

        return $this->success($comment, 'Yorumunuz başarıyla kaydedildi.', 201);
    }

    /**
     * Doğrulanmış Nümizmat Onayı (Expert Verification).
     */
    public function verify(Request $request, int $id): JsonResponse
    {
        $identification = IdentificationRequest::find($id);
        if (!$identification) {
            return $this->error('Talep bulunamadı.', 404);
        }

        $user = $request->user();
        if (!$user->isExpert()) {
            return $this->error('Yalnızca Doğrulanmış Nümizmat veya Editörler talebi onaylayabilir.', 403);
        }

        $validated = $request->validate([
            'coin_id' => ['required', 'exists:coins,id'],
            'expert_notes' => ['nullable', 'string', 'max:1000', new NoIllegalTradingRule()],
        ]);

        $identification->update([
            'status' => 'identified',
            'identified_coin_id' => $validated['coin_id'],
            'verified_by_user_id' => $user->id,
            'expert_notes' => $validated['expert_notes'] ?? 'Doğrulanmış Nümizmat tarafından onaylandı.',
        ]);

        return $this->success($identification, 'Sikke kesinleştirildi ve talep onaylandı.');
    }
}

