<?php

namespace App\Services;

use App\Models\Coin;
use App\Models\CoinReference;
use Illuminate\Support\Collection;

class ConcordanceResolverService
{
    /**
     * Akademik katalog kısaltma ve takma ad (alias) haritası.
     * @var array<string, string>
     */
    protected array $systemAliases = [
        'ric'       => 'RIC',
        'r.i.c'     => 'RIC',
        'r.i.c.'    => 'RIC',
        'crawford'  => 'Crawford',
        'cr'        => 'Crawford',
        'cr.'       => 'Crawford',
        'rrc'       => 'Crawford',
        'sear'      => 'Sear',
        's'         => 'Sear',
        's.'        => 'Sear',
        'bmc'       => 'BMC',
        'bmcre'     => 'BMC',
        'b.m.c'     => 'BMC',
        'b.m.c.'    => 'BMC',
        'cohen'     => 'Cohen',
        'coh'       => 'Cohen',
        'coh.'      => 'Cohen',
        'c'         => 'Cohen',
        'c.'        => 'Cohen',
        'babelon'   => 'Babelon',
        'bab'       => 'Babelon',
        'bab.'      => 'Babelon',
        'sng'       => 'SNG',
        'price'     => 'Price',
        'pr'        => 'Price',
        'pr.'       => 'Price',
        'muller'    => 'Muller',
        'müller'    => 'Muller',
        'mul'       => 'Muller',
        'rsc'       => 'RSC',
        'pere'      => 'Pere',
        'damali'    => 'Damali',
        'damalı'    => 'Damali',
        'artuk'     => 'Artuk',
    ];

    /**
     * Kullanıcı sorgusunu analiz edip bir katalog referansı olup olmadığını ayrıştırır.
     *
     * @param string $term Örn: "RIC 115", "Sear 1850", "Crawford 335/1c"
     * @return array{system: string, reference: string, raw_term: string}|null
     */
    public function parse(string $term): ?array
    {
        $cleanTerm = trim(preg_replace('/\s+/', ' ', $term));

        if (empty($cleanTerm)) {
            return null;
        }

        // 1. Regex: [Katalog Adı veya Kısaltması] + [Boşluk / Tire / Nokta] + [Referans Kodu]
        // Örnekler: "RIC II 115", "RIC 115", "Crawford 335/1c", "Sear 1850", "BMC 220"
        $pattern = '/^\s*([A-Za-zçğıöşüÇĞİÖŞÜ\.]+)\s*[\-\s\.]+\s*(.+)$/u';

        if (preg_match($pattern, $cleanTerm, $matches)) {
            $prefix = strtolower(rtrim($matches[1], '.'));
            $refNumber = trim($matches[2]);

            // Alias haritasında var mı kontrol et
            if (isset($this->systemAliases[$prefix])) {
                $canonicalSystem = $this->systemAliases[$prefix];

                return [
                    'system' => $canonicalSystem,
                    'reference' => $refNumber,
                    'raw_term' => $cleanTerm,
                ];
            }
        }

        // 2. Özel SNG tespiti (Örn: "SNG Cop 450", "SNG ANS 120")
        if (preg_match('/^SNG\s+([A-Za-z]+)\s+([0-9a-zA-Z\/\-]+)$/i', $cleanTerm, $sngMatches)) {
            return [
                'system' => 'SNG',
                'reference' => trim($sngMatches[1] . ' ' . $sngMatches[2]),
                'raw_term' => $cleanTerm,
            ];
        }

        return null;
    }

    /**
     * Verilen metin bir nümizmatik referans ise ilgili sikkeyi doğrudan çözer.
     *
     * @param string $term
     * @return array{coin: Coin|null, is_direct_match: bool, system: string|null, reference: string|null, alternatives: Collection}
     */
    public function resolve(string $term): array
    {
        $parsed = $this->parse($term);

        if (!$parsed) {
            return [
                'coin' => null,
                'is_direct_match' => false,
                'system' => null,
                'reference' => null,
                'alternatives' => collect(),
            ];
        }

        $system = $parsed['system'];
        $refNumber = $parsed['reference'];

        // 1. Aşama: coin_references tablosunda birebir tam eşleşme (Tam concordance)
        $exactRef = CoinReference::with(['coin.primaryImage', 'coin.period', 'coin.ruler'])
            ->where('catalog_system', $system)
            ->where(function ($q) use ($refNumber) {
                $q->where('reference_number', $refNumber)
                  // Roma rakamlı / alt ciltli varyasyonlar (Örn: "II 142" veya "142")
                  ->orWhere('reference_number', 'LIKE', "%{$refNumber}")
                  ->orWhere('reference_number', 'LIKE', "{$refNumber}%");
            })
            ->first();

        if ($exactRef && $exactRef->coin) {
            return [
                'coin' => $exactRef->coin,
                'is_direct_match' => true,
                'system' => $system,
                'reference' => $exactRef->reference_number,
                'alternatives' => collect([$exactRef->coin]),
            ];
        }

        // 2. Aşama: coins tablosundaki ana catalog_reference alanında arama
        $coinByCatalogRef = Coin::with(['primaryImage', 'period', 'ruler'])
            ->where('catalog_reference', 'LIKE', "%{$system}%")
            ->where('catalog_reference', 'LIKE', "%{$refNumber}%")
            ->first();

        if ($coinByCatalogRef) {
            return [
                'coin' => $coinByCatalogRef,
                'is_direct_match' => true,
                'system' => $system,
                'reference' => $refNumber,
                'alternatives' => collect([$coinByCatalogRef]),
            ];
        }

        // 3. Aşama: Kısmi eşleşmeler (Birden fazla sikke aynı alt seri numarasını paylaşıyorsa)
        $matches = CoinReference::with(['coin.primaryImage', 'coin.period', 'coin.ruler'])
            ->where('catalog_system', $system)
            ->where('reference_number', 'LIKE', "%{$refNumber}%")
            ->take(5)
            ->get()
            ->pluck('coin')
            ->filter()
            ->unique('id')
            ->values();

        return [
            'coin' => $matches->first(),
            'is_direct_match' => $matches->count() === 1,
            'system' => $system,
            'reference' => $refNumber,
            'alternatives' => $matches,
        ];
    }
}

