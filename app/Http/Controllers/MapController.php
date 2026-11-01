<?php

namespace App\Http\Controllers;

use App\Models\Mint;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * İnteraktif Antik Darphaneler, Roma İmparatorluk Sınırları ve Ticaret Rotaları Haritası.
     */
    public function index(Request $request)
    {
        $selectedMintSlug = $request->query('mint');

        $mints = Mint::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('coins')
            ->orderBy('name')
            ->get();

        $selectedMint = null;
        if ($selectedMintSlug) {
            $selectedMint = $mints->firstWhere('slug', $selectedMintSlug);
        }

        return view('map.index', [
            'mints' => $mints,
            'selectedMint' => $selectedMint,
            'selectedMintSlug' => $selectedMintSlug,
        ]);
    }
}

