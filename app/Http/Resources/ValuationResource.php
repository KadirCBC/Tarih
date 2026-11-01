<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValuationResource extends JsonResource
{
    /**
     * Transform the resource into an array with multi-currency conversion.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $grade = $this->condition_grade?->value ?? $this->condition_grade;
        $user = $request->user('sanctum') ?? $request->user();
        $isPro = $user ? $user->isPro() : false;

        // Free kullanıcılara yalnızca Poor ve Fine açık; VF, XF ve UNC Pro kilitli
        $isLocked = !$isPro && in_array($grade, ['VF', 'XF', 'UNC']);

        $currencyService = app(CurrencyService::class);
        $activeCurrency = $currencyService->detectUserCurrency($request);

        $minUsd = (float) $this->min_value_usd;
        $maxUsd = (float) $this->max_value_usd;

        // Kullanıcının tercih ettiği para birimine dönüştür
        $converted = $currencyService->convertRange($minUsd, $maxUsd, $activeCurrency);

        return [
            'id' => $this->id,
            'condition_grade' => $grade,
            'condition_label' => $this->condition_grade?->label() ?? null,
            'is_locked' => $isLocked,
            
            // Aktif para birimi değerleri
            'currency' => $activeCurrency,
            'currency_symbol' => $converted['symbol'],
            'min_value' => $isLocked ? null : $converted['min'],
            'max_value' => $isLocked ? null : $converted['max'],
            'price_range_formatted' => $isLocked ? '🔒 Nümizmat Pro' : $converted['formatted'],

            // Temel USD referansı
            'base_usd' => [
                'min' => $isLocked ? null : $minUsd,
                'max' => $isLocked ? null : $maxUsd,
                'formatted' => $isLocked ? '🔒' : '$' . number_format($minUsd) . ' - $' . number_format($maxUsd),
            ],

            // Çoklu para birimi tablosu (Mobil uygulama hızlı döviz geçişi için)
            'multi_currency' => $isLocked ? null : [
                'USD' => $currencyService->convertRange($minUsd, $maxUsd, 'USD'),
                'EUR' => $currencyService->convertRange($minUsd, $maxUsd, 'EUR'),
                'TRY' => $currencyService->convertRange($minUsd, $maxUsd, 'TRY'),
            ],

            'last_updated_at' => $this->last_updated_at?->format('Y-m-d'),
        ];
    }
}
