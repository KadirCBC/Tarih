<?php

namespace App\Http\Controllers;

use App\Services\RegnalDateConverterService;
use Illuminate\Http\Request;

class RegnalCalculatorController extends Controller
{
    public function __construct(
        protected RegnalDateConverterService $converter
    ) {}

    /**
     * Web Arayüzü: Antik Sikke Tarih ve Regnal Yıl Hesaplama Sayfası.
     * GET /tarih-hesaplayici
     */
    public function index(Request $request)
    {
        $romanEmperors = $this->converter->getSupportedRomanEmperors();
        $ottomanSultans = $this->converter->getSupportedOttomanSultans();

        // Varsayılan hesaplama (Örnek: Trajan TR P IX COS V)
        $defaultCalculation = $this->converter->calculate([
            'system' => 'roman',
            'ruler' => 'trajan',
            'tr_p' => 9,
            'cos' => 5,
        ]);

        return view('calculator.index', compact('romanEmperors', 'ottomanSultans', 'defaultCalculation'));
    }

    /**
     * Web Form Gönderimi (veya AJAX hesaplama).
     * POST /tarih-hesaplayici
     */
    public function calculate(Request $request)
    {
        $result = $this->converter->calculate($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('calculator.index')->with('calculation_result', $result);
    }
}

