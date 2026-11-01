<?php

namespace App\Services\Epigraphy;

use App\Models\Coin;
use Illuminate\Support\Collection;

class LegendReconstructionService
{
    /**
     * Kullanıcının girdiği eksik/aşınmış lejant parçacıklarını standart
     * nümizmatik epigrafik kayıtlarla eşleştirip tamamlar.
     *
     * @param string $input Örn: "...NERV...TR...PARTH...", "NERV*TRAI*"
     * @param string $face 'obverse', 'reverse', veya 'both'
     * @param int $limit Döndürülecek en yüksek olasılıklı aday sayısı (varsayılan: 5)
     * @return array<int, array<string, mixed>>
     */
    public function reconstruct(string $input, string $face = 'both', int $limit = 5): array
    {
        $normalizedInput = $this->normalizeInput($input);
        $searchTokens = $this->extractTokens($normalizedInput);

        if (empty($searchTokens)) {
            return [];
        }

        // Veritabanındaki benzersiz lejant kayıtlarını getir
        $candidateCoins = $this->fetchCandidateCoins($searchTokens, $face);

        $results = [];

        foreach ($candidateCoins as $coin) {
            $legendsToCheck = [];
            if (in_array($face, ['obverse', 'both']) && !empty($coin->obverse_legend)) {
                $legendsToCheck[] = [
                    'face' => 'obverse',
                    'legend' => $coin->obverse_legend,
                    'translation' => $coin->translated_obverse_legend_trans,
                ];
            }
            if (in_array($face, ['reverse', 'both']) && !empty($coin->reverse_legend)) {
                $legendsToCheck[] = [
                    'face' => 'reverse',
                    'legend' => $coin->reverse_legend,
                    'translation' => $coin->translated_reverse_legend_trans,
                ];
            }

            foreach ($legendsToCheck as $item) {
                $targetLegend = $item['legend'];
                $scoreData = $this->calculateEpigraphicMatchScore($normalizedInput, $searchTokens, $targetLegend);

                if ($scoreData['score'] >= 35) { // Minimum eşik
                    $reconstructedDisplay = $this->highlightReconstructedText($searchTokens, $targetLegend);

                    $results[] = [
                        'coin_id' => $coin->id,
                        'coin_title' => $coin->translated_title,
                        'coin_slug' => $coin->slug,
                        'period' => $coin->period?->name,
                        'ruler' => $coin->ruler?->name,
                        'face' => $item['face'],
                        'original_legend' => $targetLegend,
                        'reconstructed_text' => $reconstructedDisplay,
                        'formatted_feedback' => "Tespit edilen yazı: {$reconstructedDisplay} - %{$scoreData['score']} Eşleşme",
                        'confidence_score' => $scoreData['score'],
                        'legend_translation' => $item['translation'],
                        'primary_image' => $coin->primaryImage?->url,
                        'catalog_reference' => $coin->catalog_reference,
                    ];
                }
            }
        }

        // Skorlarına göre sırala ve tekrarları temizle
        return collect($results)
            ->sortByDesc('confidence_score')
            ->unique(fn ($item) => $item['original_legend'] . $item['face'])
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Girdiyi nümizmatik epigrafiye uygun şekilde temizler ve normalize eder.
     */
    public function normalizeInput(string $input): string
    {
        $input = mb_strtoupper(trim($input), 'UTF-8');

        // Üç nokta, yıldız, soru işareti veya yüzde işaretlerini standart wildcard sembolüne çevir
        $input = str_replace(['…', '...', '..', '*', '%', '?'], '.*', $input);

        // Ardışık jokerleri teke indir
        $input = preg_replace('/(\.\*)+/', '.*', $input);

        return trim($input);
    }

    /**
     * Girdideki net okunan anahtar kelimeleri/harf öbeklerini çıkarır.
     *
     * @return array<string>
     */
    public function extractTokens(string $normalized): array
    {
        $parts = preg_split('/\.\*|\s+/', $normalized);
        $tokens = [];

        foreach ($parts as $part) {
            $clean = preg_replace('/[^\p{L}\p{N}]+/u', '', $part);
            if (mb_strlen($clean) >= 2) { // En az 2 harfli anlamlı epigrafik bloklar (Örn: TR, AVG, COS)
                $tokens[] = $clean;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Potansiyel aday sikkeleri veritabanından çeker.
     */
    protected function fetchCandidateCoins(array $tokens, string $face): Collection
    {
        return Coin::query()
            ->with(['period:id,name', 'ruler:id,name', 'primaryImage'])
            ->where(function ($query) use ($tokens, $face) {
                foreach ($tokens as $token) {
                    if (in_array($face, ['obverse', 'both'])) {
                        $query->orWhere('obverse_legend', 'like', "%{$token}%");
                    }
                    if (in_array($face, ['reverse', 'both'])) {
                        $query->orWhere('reverse_legend', 'like', "%{$token}%");
                    }
                }
            })
            ->limit(50)
            ->get();
    }

    /**
     * Regex, Token Kapsamı ve Levenshtein ile epigrafik eşleşme skorunu (%0 - %100) hesaplar.
     *
     * @param string $normalizedPattern Örn: ".*NERV.*TR.*PARTH.*"
     * @param array<string> $tokens
     * @param string $legend Orijinal hedef lejant
     * @return array{score: int, regex_matched: bool}
     */
    public function calculateEpigraphicMatchScore(string $normalizedPattern, array $tokens, string $legend): array
    {
        $cleanTarget = mb_strtoupper(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $legend), 'UTF-8');
        $targetWords = preg_split('/\s+/', $cleanTarget) ?: [];

        // 1. Sıralı Regex Deseni Kontrolü
        $regexParts = array_map(fn ($t) => preg_quote($t, '/'), $tokens);
        $regex = '/' . implode('.*', $regexParts) . '/i';
        $regexMatched = (bool) preg_match($regex, $cleanTarget);

        // 2. Token Bulunurluk Oranı
        $matchedTokensCount = 0;
        foreach ($tokens as $token) {
            if (mb_stripos($cleanTarget, $token) !== false) {
                $matchedTokensCount++;
            }
        }

        $tokenCoverage = count($tokens) > 0 ? ($matchedTokensCount / count($tokens)) : 0;

        // 3. Levenshtein / Karakter Benzerliği
        $combinedTokens = implode(' ', $tokens);
        similar_text($combinedTokens, $cleanTarget, $similarityPercent);

        // 4. Ağırlıklı Puanlama Algoritması
        // - Sıralı Regex eşleşmesi: 45 puan
        // - Token kapsamı: 40 puan
        // - Metin benzerliği: 15 puan
        $score = 0;
        if ($regexMatched) {
            $score += 45;
        }

        $score += (int) round($tokenCoverage * 40);
        $score += (int) round(($similarityPercent / 100) * 15);

        // Eğer tüm tokenlar birebir ve sıralı mevcutsa ve hedef metin çok uzamıyorsa %90+ skor ver
        if ($regexMatched && $tokenCoverage >= 1.0) {
            $score = max($score, 92);
        }

        return [
            'score' => min(100, max(0, $score)),
            'regex_matched' => $regexMatched,
        ];
    }

    /**
     * Orijinal lejant üzerindeki kullanıcı girdilerini tespit edip,
     * tamamlanan eksik kısımları köşeli parantez içine alır:
     * Örn: "[IMP CAES ]NERV[A ]TR[AIAN AVG GERM DAC ]PARTH[ICO P M TR P COS VI P P]"
     */
    public function highlightReconstructedText(array $tokens, string $originalLegend): string
    {
        if (empty($tokens) || empty($originalLegend)) {
            return $originalLegend;
        }

        $legendUpper = mb_strtoupper($originalLegend, 'UTF-8');
        $mask = array_fill(0, mb_strlen($legendUpper), false);

        // Her bulunan tokenın indeks aralığını maskele
        $searchOffset = 0;
        foreach ($tokens as $token) {
            $pos = mb_stripos($legendUpper, $token, $searchOffset);
            if ($pos !== false) {
                $len = mb_strlen($token);
                for ($i = $pos; $i < $pos + $len; $i++) {
                    $mask[$i] = true;
                }
                $searchOffset = $pos + $len;
            }
        }

        // Maske üzerinden köşeli parantezli string inşa et
        $result = '';
        $inBracket = false;
        $chars = mb_str_split($originalLegend);

        foreach ($chars as $idx => $char) {
            $isFound = $mask[$idx] ?? false;

            if (!$isFound && !$inBracket) {
                $result .= '[';
                $inBracket = true;
            } elseif ($isFound && $inBracket) {
                $result .= ']';
                $inBracket = false;
            }

            $result .= $char;
        }

        if ($inBracket) {
            $result .= ']';
        }

        return $result;
    }
}

