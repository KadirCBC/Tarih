<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::withCount(['coins', 'rulers'])
            ->orderBy('start_year')
            ->get();

        return view('periods.index', compact('periods'));
    }

    public function show(Period $period)
    {
        $period->load(['rulers' => fn ($q) => $q->withCount('coins')]);

        $coins = $period->coins()
            ->with(['ruler', 'primaryImage', 'valuations'])
            ->paginate(24);

        return view('periods.show', compact('period', 'coins'));
    }
}

