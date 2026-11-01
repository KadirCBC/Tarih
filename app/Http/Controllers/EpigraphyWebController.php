<?php

namespace App\Http\Controllers;

use App\Services\Epigraphy\CircularTextUnwrapperService;
use App\Services\Epigraphy\LegendReconstructionService;
use Illuminate\Http\Request;

class EpigraphyWebController extends Controller
{
    public function __construct(
        protected LegendReconstructionService $reconstructionService,
        protected CircularTextUnwrapperService $unwrapperService
    ) {}

    /**
     * Epigrafik lejant tamamlama ve dairesel yazı çözücü arayüzü.
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if (!empty($query)) {
            $results = $this->reconstructionService->reconstruct($query, 'both', 6);
        }

        return view('epigraphy.index', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    /**
     * Fotoğraftan dairesel düzleştirme ve tamamlama işlemi.
     */
    public function reconstructImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:12288'],
            'face' => ['nullable', 'string', 'in:obverse,reverse,both'],
        ]);

        $result = $this->unwrapperService->unwrapAndReconstruct(
            $request->file('image'),
            $request->input('face', 'obverse')
        );

        return view('epigraphy.index', [
            'imageResult' => $result,
            'query' => $result['detected_raw_text'] ?? '',
            'results' => $result['all_candidates'] ?? [],
        ]);
    }
}

