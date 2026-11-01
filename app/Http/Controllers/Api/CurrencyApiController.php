<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    /**
     * Güncel ECB döviz kurlarını ve desteklenen para birimlerini listeler.
     */
    public function index(Request $request): JsonResponse
    {
        $rates = $this->currencyService->getCachedRates();
        $userCurrency = $this->currencyService->detectUserCurrency($request);

        return $this->success([
            'base_currency' => 'USD',
            'user_currency' => $userCurrency,
            'rates' => $rates,
            'supported' => array_keys(CurrencyService::SUPPORTED_CURRENCIES),
        ], 'Döviz kurları başarıyla getirildi.');
    }

    /**
     * İki para birimi arasında anlık dönüştürme yapar.
     */
    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'from' => ['nullable', 'string', 'size:3'],
            'to' => ['nullable', 'string', 'size:3'],
        ]);

        $amount = (float) $validated['amount'];
        $from = $validated['from'] ?? 'USD';
        $to = $validated['to'] ?? $this->currencyService->detectUserCurrency($request);

        $converted = $this->currencyService->convert($amount, $from, $to);
        $symbol = CurrencyService::SUPPORTED_CURRENCIES[strtoupper($to)]['symbol'] ?? '';

        return $this->success([
            'amount' => $amount,
            'from' => strtoupper($from),
            'to' => strtoupper($to),
            'result' => $converted,
            'formatted' => $this->currencyService->formatRange($converted, $converted, $symbol, strtoupper($to)),
        ]);
    }
}

