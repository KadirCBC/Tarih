<?php

namespace App\Http\Controllers;

use App\Models\Ruler;
use Illuminate\Http\Request;

class RulerController extends Controller
{
    public function index()
    {
        $rulers = Ruler::with(['period'])
            ->withCount('coins')
            ->orderBy('reign_start')
            ->paginate(30);

        return view('rulers.index', compact('rulers'));
    }

    public function show(Ruler $ruler)
    {
        $ruler->load('period');

        $coins = $ruler->coins()
            ->with(['mint', 'primaryImage', 'valuations'])
            ->paginate(24);

        return view('rulers.show', compact('ruler', 'coins'));
    }
}

