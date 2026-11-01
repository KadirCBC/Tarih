<?php

namespace App\Http\Controllers;

use App\Enums\ConditionGrade;
use App\Models\Coin;
use App\Models\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class UserCollectionController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->collections()
            ->with(['coin.primaryImage', 'coin.period', 'coin.ruler'])
            ->latest()
            ->paginate(20);

        return view('collection.index', compact('items'));
    }

    public function store(Request $request, Coin $coin)
    {
        $validated = $request->validate([
            'user_condition' => ['required', new Enum(ConditionGrade::class)],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->collections()->create([
            'coin_id' => $coin->id,
            'user_condition' => $validated['user_condition'],
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Sikke şahsi envanterinize eklendi.');
    }

    public function destroy(Request $request, UserCollection $userCollection)
    {
        abort_unless($userCollection->user_id === $request->user()->id, 403);

        $userCollection->delete();

        return back()->with('success', 'Sikke envanterinizden kaldırıldı.');
    }
}

