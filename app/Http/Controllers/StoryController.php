<?php

namespace App\Http\Controllers;

use App\Models\CoinStory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    /**
     * Nümizmatik Editoryal Hikayeler ve Haftanın Sikkesi Listesi
     */
    public function index(): View
    {
        $featuredStory = CoinStory::coinOfTheWeek()->first() 
            ?? CoinStory::published()->first();

        $stories = CoinStory::published()
            ->when($featuredStory, fn ($q) => $q->where('id', '!=', $featuredStory->id))
            ->paginate(12);

        return view('stories.index', compact('featuredStory', 'stories'));
    }

    /**
     * Google Discover Uyumlu Sikke Hikayesi Detay Sayfası
     */
    public function show(CoinStory $story): View
    {
        $story->incrementViews();
        $story->load(['coin.period', 'coin.ruler', 'coin.mint', 'coin.primaryImage', 'coin.valuations']);

        $relatedStories = CoinStory::published()
            ->where('id', '!=', $story->id)
            ->limit(3)
            ->get();

        return view('stories.show', compact('story', 'relatedStories'));
    }

    /**
     * Doğrudan güncel "Haftanın Sikkesi"ne yönlendiren kısa URL
     */
    public function coinOfTheWeek()
    {
        $story = CoinStory::coinOfTheWeek()->first() ?? CoinStory::published()->first();

        if ($story) {
            return redirect()->route('stories.show', $story->slug);
        }

        return redirect()->route('stories.index');
    }
}

