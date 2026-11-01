<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\IdentificationComment;
use App\Models\IdentificationRequest;
use App\Rules\NoIllegalTradingRule;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentificationRequestController extends Controller
{
    /**
     * Sikke Kimlikleme Kulübü: Tüm açık ve onaylanmış talepleri listeler.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = IdentificationRequest::with(['user', 'identifiedCoin.primaryImage', 'verifiedBy'])
            ->withCount('comments');

        if ($status !== 'all' && in_array($status, ['pending', 'identified', 'disputed', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->latest('id')->paginate(12)->withQueryString();

        $counts = [
            'all' => IdentificationRequest::count(),
            'pending' => IdentificationRequest::where('status', 'pending')->count(),
            'identified' => IdentificationRequest::where('status', 'identified')->count(),
            'disputed' => IdentificationRequest::where('status', 'disputed')->count(),
        ];

        return view('identifications.index', compact('requests', 'status', 'counts'));
    }

    /**
     * Yeni kimlikleme talebi oluşturma formu.
     */
    public function create()
    {
        return view('identifications.create');
    }

    /**
     * Yeni kimlikleme talebini kaydeder.
     * 2863 Sayılı Kanun gereği alım-satım ve definecilik ifadeleri otomatik engellenir.
     */
    public function store(Request $request, ImageOptimizationService $optimizer)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:150', new NoIllegalTradingRule()],
            'description' => ['nullable', 'string', 'max:1000', new NoIllegalTradingRule()],
            'obverse_image' => ['required', 'image', 'mimes:jpeg,png,webp,jpg', 'max:12288'],
            'reverse_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:12288'],
            'weight_g' => ['nullable', 'numeric', 'min:0.1', 'max:500'],
            'diameter_mm' => ['nullable', 'numeric', 'min:1', 'max:150'],
            'legal_consent' => ['accepted'],
        ], [
            'legal_consent.accepted' => '2863 sayılı kanun kapsamında sikkenin alım-satım amaçlı olmadığını onaylamalısınız.',
        ]);

        $userId = Auth::id() ?? 1; // Oturum yoksa demo id

        // Görselleri WebP formatında optimize et
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
            'user_id' => $userId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'obverse_image' => $obversePath,
            'reverse_image' => $reversePath,
            'weight_g' => $validated['weight_g'] ?? null,
            'diameter_mm' => $validated['diameter_mm'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('identifications.show', $identification)
            ->with('success', 'Kimlikleme talebiniz Nümizmatik Kulübü’ne iletildi. Uzmanlar ve topluluk inceleyecektir.');
    }

    /**
     * Talep detay sayfası, topluluk önerileri ve uzman inceleme alanı.
     */
    public function show(IdentificationRequest $identification)
    {
        $identification->load([
            'user',
            'identifiedCoin.primaryImage',
            'identifiedCoin.period',
            'identifiedCoin.ruler',
            'verifiedBy',
            'comments.user',
            'comments.suggestedCoin.primaryImage',
        ]);

        // Arama önerisi için hızlı sikke listesi (modal veya autocomplete için)
        $popularCoins = Coin::select(['id', 'title', 'slug', 'catalog_reference'])
            ->latest('id')
            ->limit(20)
            ->get();

        return view('identifications.show', compact('identification', 'popularCoins'));
    }

    /**
     * Topluluk önerisi / yorumu ekleme.
     */
    public function addComment(Request $request, IdentificationRequest $identification)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:3', 'max:1500', new NoIllegalTradingRule()],
            'suggested_coin_id' => ['nullable', 'exists:coins,id'],
        ]);

        $user = Auth::user();
        $isExpert = $user ? $user->isExpert() : false;

        IdentificationComment::create([
            'identification_request_id' => $identification->id,
            'user_id' => $user ? $user->id : 1,
            'suggested_coin_id' => $validated['suggested_coin_id'] ?? null,
            'comment' => $validated['comment'],
            'is_expert_verification' => $isExpert,
        ]);

        return redirect()->route('identifications.show', $identification)
            ->with('success', 'Teşhis ve yorumunuz kulübe eklendi.');
    }

    /**
     * Doğrulanmış Nümizmat / Editör onayı:
     * Talebi kapatır ve doğru katalog sikkesiyle eşleştirir.
     */
    public function verify(Request $request, IdentificationRequest $identification)
    {
        $user = Auth::user();

        // Uzmanlık / Editör denetimi
        if (!$user || !$user->isExpert()) {
            return redirect()->back()->with('error', 'Yalnızca Doğrulanmış Nümizmat veya Editörler kesin onay verebilir.');
        }

        $validated = $request->validate([
            'coin_id' => ['required', 'exists:coins,id'],
            'expert_notes' => ['nullable', 'string', 'max:1000', new NoIllegalTradingRule()],
        ]);

        $identification->update([
            'status' => 'identified',
            'identified_coin_id' => $validated['coin_id'],
            'verified_by_user_id' => $user->id,
            'expert_notes' => $validated['expert_notes'] ?? 'Doğrulanmış Nümizmat tarafından katalogla eşleştirildi.',
        ]);

        // Onay yorumu olarak da kaydet
        IdentificationComment::create([
            'identification_request_id' => $identification->id,
            'user_id' => $user->id,
            'suggested_coin_id' => $validated['coin_id'],
            'comment' => "✅ [UZMAN ONAYI]: " . ($validated['expert_notes'] ?? 'Bu sikke katalog kaydıyla eşleştirildi ve talep onaylandı.'),
            'is_expert_verification' => true,
        ]);

        return redirect()->route('identifications.show', $identification)
            ->with('success', 'Sikke kimliği uzman onayınızla kesinleştirildi ve katalogla eşleştirildi.');
    }

    /**
     * Talebi tartışmalı veya sahte/replika olarak işaretleme.
     */
    public function dispute(Request $request, IdentificationRequest $identification)
    {
        $user = Auth::user();
        if (!$user || !$user->isExpert()) {
            return redirect()->back()->with('error', 'Bu işlemi yalnızca uzman nümizmatlar yapabilir.');
        }

        $status = $request->input('status', 'disputed');
        if (!in_array($status, ['disputed', 'rejected'])) {
            $status = 'disputed';
        }

        $notes = $request->input('notes', 'Uzman incelemesinde şüpheli veya tartışmalı bulunmuştur.');

        $identification->update([
            'status' => $status,
            'verified_by_user_id' => $user->id,
            'expert_notes' => $notes,
        ]);

        return redirect()->route('identifications.show', $identification)
            ->with('success', "Talep durumu '{$identification->status_badge['label']}' olarak güncellendi.");
    }
}

