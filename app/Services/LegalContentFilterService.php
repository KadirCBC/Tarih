<?php

namespace App\Services;

class LegalContentFilterService
{
    /**
     * 2863 Sayılı Kanun ve Nümizmatik Mevzuatı Uyarınca Yasaklı Kelime ve İfade Kalıpları
     */
    protected array $prohibitedPatterns = [
        // Ticaret, Alım-Satım ve Pazarlık Kalıpları
        '/\b(sat[ıi]l[ıi]k|sat[ıi]yorum|satmak|sataca[gğ][ıi]m|sat[ıi]c[ıi]|sat[ıi][sş])\b/iu',
        '/\b(fiyat[ıi]\s*ne\s*kadar\s*verirsin|ka[cç]a\s*gider|ka[cç]a\s*sat[ıi]l[ıi]r|ka[cç]a\s*al[ıi]rs[ıi]n)\b/iu',
        '/\b(al[ıi]c[ıi]\s*aran[ıi]yor|teklif\s*ver|teklif\s*al[ıi]n[ıi]r|elden\s*sat[ıi][sş])\b/iu',
        '/\b(ne\s*kadar\s*eder|de[gğ]eri\s*nedir\s*sataca[gğ][ıi]m)\b/iu',

        // Definecilik, Kaçak Kazı ve Dedektör Kalıpları
        '/\b(define|defineci|definecilik|hazine|hazineci)\b/iu',
        '/\b(dedekt[oö]r|alan\s*tarama|yeralt[ıi]\s*g[oö]r[uü]nt[uü]leme)\b/iu',
        '/\b(nereden\s*buldum|tarlada\s*buldum|ba[gğ]da\s*buldum|bah[cç]ede\s*buldum|da[gğ]da\s*buldum)\b/iu',
        '/\b(kaz[ıi]da\s*buldum|ka[cç]ak\s*kaz[ıi]|t[uü]m[uü]l[uü]s|h[oö]y[uü]k|mezar\s*a[cç]t[ıi]k)\b/iu',
        '/\b(topraktan\s*[cç][ıi]kard[ıi]m|k[uü]p\s*i[cç]inde\s*buldum|yeni\s*[cç][ıi]kt[ıi])\b/iu',
    ];

    /**
     * Metinde yasaklı kelime veya alım-satım / definecilik ifadesi var mı kontrol eder.
     */
    public function containsProhibitedContent(?string $text): bool
    {
        if (empty($text)) {
            return false;
        }

        foreach ($this->prohibitedPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tespit edilen yasaklı ifadeleri listeler.
     */
    public function detectViolations(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        $detected = [];
        foreach ($this->prohibitedPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $detected = array_merge($detected, $matches[0]);
            }
        }

        return array_unique($detected);
    }

    /**
     * Kullanıcı için standart ret / uyarı mesajı.
     */
    public function getWarningMessage(): string
    {
        return '2863 sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu uyarınca platformumuzda sikke alım-satımı, fiyat teklifi, definecilik veya dedektör araması içeren ifadeler kesinlikle yasaktır.';
    }
}

