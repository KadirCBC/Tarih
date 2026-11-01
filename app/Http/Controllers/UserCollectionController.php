<?php

namespace App\Http\Controllers;

use App\Enums\ConditionGrade;
use App\Models\Coin;
use App\Models\UserCollection;
use App\Services\CollectionPortfolioService;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response;

class UserCollectionController extends Controller
{
    protected CollectionPortfolioService $portfolioService;

    public function __construct(CollectionPortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(Request $request)
    {
        $items = $request->user()->collections()
            ->with(['coin.primaryImage', 'coin.period', 'coin.ruler', 'coin.valuations'])
            ->latest()
            ->paginate(20);

        $summary = $this->portfolioService->getPortfolioSummary($request->user());

        return view('collection.index', compact('items', 'summary'));
    }

    public function store(Request $request, Coin $coin, ImageOptimizationService $imageService)
    {
        $user = $request->user();

        if (!$user->canAddToCollection()) {
            return back()->with('error', 'Ücretsiz planda maksimum 5 sikke ekleyebilirsiniz. Nümizmat Pro\'ya geçerek sınırsız envanter oluşturabilirsiniz.');
        }

        $validated = $request->validate([
            'user_condition' => ['required', new Enum(ConditionGrade::class)],
            'purchase_price_usd' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000', new \App\Rules\NoIllegalTradingRule],
            'custom_image' => ['nullable', 'image', 'max:10240'],
            'legal_declaration' => ['required_with:custom_image', 'accepted'],
        ], [
            'legal_declaration.accepted' => 'Özel sikke görseli yüklemek için yasal hak beyanını onaylamalısınız.',
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

        $request->user()->collections()->create([
            'coin_id' => $coin->id,
            'user_condition' => $validated['user_condition'],
            'custom_image_path' => $customImagePath,
            'purchase_price_usd' => $validated['purchase_price_usd'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Sikke şahsi koleksiyonunuza eklendi.');
    }

    public function exportPdf(Request $request): Response
    {
        $pdf = $this->portfolioService->generateInventoryPdf($request->user());
        $fileName = 'Numizmatik_Envanter_Sertifikasi_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function destroy(Request $request, UserCollection $userCollection)
    {
        abort_unless($userCollection->user_id === $request->user()->id, 403);

        $userCollection->delete();

        return back()->with('success', 'Sikke envanterinizden kaldırıldı.');
    }
}
