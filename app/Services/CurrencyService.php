<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;

class CurrencyService
{
    /**
     * Avrupa Merkez Bankası (ECB) günlük döviz kuru XML adresi.
     */
    public const ECB_FEED_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Desteklenen ve takip edilen para birimleri.
     */
    public const SUPPORTED_CURRENCIES = [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        'TRY' => ['name' => 'Türk Lirası', 'symbol' => '₺'],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
        'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF'],
    ];

    /**
     * ECB XML beslemesinden kurları çeker ve veritabanını günceller.
     *
     * @return array<string, float>
     */
    public function fetchRatesFromEcb(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'NumismaticCatalog/1.0 (+https://antiknumismatik.com)',
                    'Accept' => 'application/xml, text/xml',
                ])
                ->get(self::ECB_FEED_URL);

            if (!$response->successful()) {
                throw new \Exception("ECB HTTP Hatası: Kod " . $response->status());
            }

            $xml = new SimpleXMLElement($response->body());
            $rates = [];

            // ECB XML namespaces
            $xml->registerXPathNamespace('gesmes', 'http://www.gesmes.org/xml/2002-08-01');
            $xml->registerXPathNamespace('cube', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

            $cubes = $xml->xpath('//cube:Cube[@currency]');

            foreach ($cubes as $cube) {
                $currencyCode = (string) $cube['currency'];
                $rate = (float) $cube['rate'];
                $rates[$currencyCode] = $rate;
            }

            if (empty($rates['USD'])) {
                throw new \Exception("ECB beslemesinde USD kuru bulunamadı.");
            }

            $eurToUsdRate = $rates['USD']; // 1 EUR = X USD

            // Para birimlerini veritabanında kaydet veya güncelle
            foreach (self::SUPPORTED_CURRENCIES as $code => $meta) {
                if ($code === 'EUR') {
                    $rateToEur = 1.0;
                    $rateToUsd = 1.0 / $eurToUsdRate;
                } else {
                    $rateToEur = $rates[$code] ?? null;
                    if ($rateToEur === null) {
                        continue;
                    }
                    $rateToUsd = $rateToEur / $eurToUsdRate;
                }

                CurrencyRate::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $meta['name'],
                        'symbol' => $meta['symbol'],
                        'rate_to_eur' => round($rateToEur, 6),
                        'rate_to_usd' => round($rateToUsd, 6),
                        'is_active' => true,
                        'fetched_at' => now(),
                    ]
                );
            }

            Cache::forget('numismatic_currency_rates');
            Log::info("ECB Döviz kurları başarıyla güncellendi: USD: {$eurToUsdRate}, TRY: " . ($rates['TRY'] ?? 'N/A'));

            return $rates;
        } catch (Throwable $e) {
            Log::error("ECB Kurları Çekme Hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Aktif kurları Cache üzerinden getirir.
     *
     * @return array<string, array{rate_to_usd: float, rate_to_eur: float, symbol: string, name: string}>
     */
    public function getCachedRates(): array
    {
        return Cache::remember('numismatic_currency_rates', 86400, function () {
            $rates = CurrencyRate::active()->get();
            $result = [];

            foreach ($rates as $rate) {
                $result[$rate->code] = [
                    'code' => $rate->code,
                    'name' => $rate->name,
                    'symbol' => $rate->symbol,
                    'rate_to_usd' => (float) $rate->rate_to_usd,
                    'rate_to_eur' => (float) $rate->rate_to_eur,
                    'fetched_at' => $rate->fetched_at?->toIso8601String(),
                ];
            }

            // Fallback varsayılanlar
            if (empty($result)) {
                $result = [
                    'USD' => ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'rate_to_usd' => 1.0, 'rate_to_eur' => 1.085],
                    'EUR' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'rate_to_usd' => 0.9216, 'rate_to_eur' => 1.0],
                    'TRY' => ['code' => 'TRY', 'name' => 'Türk Lirası', 'symbol' => '₺', 'rate_to_usd' => 35.5, 'rate_to_eur' => 38.5],
                ];
            }

            return $result;
        });
    }

    /**
     * Bir tutarı belirli bir para biriminden diğerine dönüştürür.
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    public function convert(float $amount, string $from = 'USD', string $to = 'USD'): float
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        if ($from === $to || $amount <= 0) {
            return $amount;
        }

        $rates = $this->getCachedRates();

        // USD bazına dönüştür
        $fromUsdRate = $rates[$from]['rate_to_usd'] ?? 1.0;
        $amountInUsd = $from === 'USD' ? $amount : ($amount / $fromUsdRate);

        // Hedef para birimine dönüştür
        $toUsdRate = $rates[$to]['rate_to_usd'] ?? 1.0;
        $converted = $to === 'USD' ? $amountInUsd : ($amountInUsd * $toUsdRate);

        return round($converted, 2);
    }

    /**
     * Kondisyon aralığını kullanıcının seçtiği para birimine çevirir.
     *
     * @param float $minUsd
     * @param float $maxUsd
     * @param string $targetCurrency
     * @return array{currency: string, symbol: string, min: float, max: float, formatted: string}
     */
    public function convertRange(float $minUsd, float $maxUsd, string $targetCurrency = 'USD'): array
    {
        $rates = $this->getCachedRates();
        $targetCurrency = strtoupper($targetCurrency);
        $currencyInfo = $rates[$targetCurrency] ?? $rates['USD'];
        $symbol = $currencyInfo['symbol'] ?? '$';

        $min = $this->convert($minUsd, 'USD', $targetCurrency);
        $max = $this->convert($maxUsd, 'USD', $targetCurrency);

        return [
            'currency' => $targetCurrency,
            'symbol' => $symbol,
            'min' => $min,
            'max' => $max,
            'formatted' => $this->formatRange($min, $max, $symbol, $targetCurrency),
        ];
    }

    /**
     * Tutar aralığını para birimi sembolüyle formatlar.
     */
    public function formatRange(float $min, float $max, string $symbol, string $currency): string
    {
        $formattedMin = number_format($min, 0, ',', '.');
        $formattedMax = number_format($max, 0, ',', '.');

        if ($currency === 'USD') {
            return "{$symbol}{$formattedMin} - {$symbol}{$formattedMax}";
        }

        return "{$formattedMin} - {$formattedMax} {$symbol}";
    }

    /**
     * Kullanıcının tercih ettiği para birimini otomatik tespit eder.
     * Sıra: Query String (?currency=EUR) -> Header (X-Currency) -> Session -> Varsayılan (USD/TRY)
     *
     * @param Request|null $request
     * @return string
     */
    public function detectUserCurrency(?Request $request = null): string
    {
        $request = $request ?: request();

        // 1. URL parametresi
        if ($request->has('currency')) {
            $currency = strtoupper($request->query('currency'));
            if (array_key_exists($currency, self::SUPPORTED_CURRENCIES)) {
                return $currency;
            }
        }

        // 2. Mobil uygulama özel başlığı
        if ($request->hasHeader('X-Currency')) {
            $currency = strtoupper($request->header('X-Currency'));
            if (array_key_exists($currency, self::SUPPORTED_CURRENCIES)) {
                return $currency;
            }
        }

        // 3. Oturum veya çerez
        if (session()->has('user_currency')) {
            return session('user_currency');
        }

        // 4. IP / Dil tabanlı varsayılan
        $locale = app()->getLocale();
        if ($locale === 'tr') {
            return 'TRY';
        } elseif ($locale === 'de') {
            return 'EUR';
        }

        return 'USD';
    }
}

