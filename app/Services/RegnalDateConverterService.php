<?php

namespace App\Services;

class RegnalDateConverterService
{
    /**
     * Roma İmparatorları Konsüllük (COS) ve Tribünlük (TR P) Kronoloji Veritabanı.
     */
    protected array $romanEmperors = [
        'augustus' => [
            'name' => 'Augustus (Gaius Julius Caesar Octavianus)',
            'reign_start' => -27,
            'reign_end' => 14,
            'tr_p_base_year' => -23, // M.Ö. 23 yazında TR P almaya başladı
            'tr_p_renewal_month' => 6, // Haziran/Temmuz
            'consulates' => [
                1 => -43, 2 => -33, 3 => -31, 4 => -30, 5 => -29,
                6 => -28, 7 => -27, 8 => -26, 9 => -25, 10 => -24,
                11 => -23, 12 => -5, 13 => -2,
            ],
        ],
        'tiberius' => [
            'name' => 'Tiberius (Tiberius Caesar Augustus)',
            'reign_start' => 14,
            'reign_end' => 37,
            'tr_p_base_year' => -6, // M.Ö. 6'da ilk TR P, M.S. 4'te yeniden başladı
            'tr_p_offset' => 4, // 14 AD = TR P XVI
            'consulates' => [1 => -13, 2 => -7, 3 => 18, 4 => 21, 5 => 31],
        ],
        'claudius' => [
            'name' => 'Claudius (Tiberius Claudius Caesar Augustus)',
            'reign_start' => 41,
            'reign_end' => 54,
            'tr_p_start' => 41, // 25 Ocak 41
            'consulates' => [1 => 37, 2 => 42, 3 => 43, 4 => 47, 5 => 51],
        ],
        'nero' => [
            'name' => 'Nero (Nero Claudius Caesar Augustus)',
            'reign_start' => 54,
            'reign_end' => 68,
            'tr_p_start' => 54, // Ekim 54
            'consulates' => [1 => 55, 2 => 57, 3 => 58, 4 => 60, 5 => 68],
        ],
        'vespasian' => [
            'name' => 'Vespasian (Titus Flavius Vespasianus)',
            'reign_start' => 69,
            'reign_end' => 79,
            'tr_p_start' => 69, // 1 Temmuz 69
            'consulates' => [1 => 51, 2 => 70, 3 => 71, 4 => 72, 5 => 74, 6 => 75, 7 => 76, 8 => 77, 9 => 79],
        ],
        'titus' => [
            'name' => 'Titus (Titus Flavius Caesar Vespasianus Augustus)',
            'reign_start' => 79,
            'reign_end' => 81,
            'tr_p_start' => 71, // 71'den itibaren Sezar olarak TR P aldı
            'consulates' => [1 => 70, 2 => 72, 3 => 74, 4 => 75, 5 => 76, 6 => 77, 7 => 79, 8 => 80],
        ],
        'domitian' => [
            'name' => 'Domitian (Titus Flavius Domitianus)',
            'reign_start' => 81,
            'reign_end' => 96,
            'tr_p_start' => 81, // 13 Eylül 81
            'consulates' => [
                1 => 71, 2 => 73, 3 => 75, 4 => 76, 5 => 77, 6 => 79, 7 => 80,
                8 => 82, 9 => 83, 10 => 84, 11 => 85, 12 => 86, 13 => 87, 14 => 88,
                15 => 90, 16 => 92, 17 => 95,
            ],
        ],
        'nerva' => [
            'name' => 'Nerva (Marcus Cocceius Nerva)',
            'reign_start' => 96,
            'reign_end' => 98,
            'tr_p_start' => 96, // 18 Eylül 96
            'consulates' => [1 => 71, 2 => 90, 3 => 97, 4 => 98],
        ],
        'trajan' => [
            'name' => 'Trajan (Marcus Ulpius Traianus)',
            'reign_start' => 98,
            'reign_end' => 117,
            'tr_p_start' => 96, // 97 kışında TR P II olduğundan; TR P IX = 104/105 kışı
            'tr_p_renewal_day' => 10,
            'tr_p_renewal_month' => 12, // 10 Aralık renewal
            'consulates' => [
                1 => 91,
                2 => 98,
                3 => 100,
                4 => 101,
                5 => 103, // 103'ten 111 sonuna kadar COS V
                6 => 112, // 112'den ölümüne kadar COS VI
            ],
        ],
        'hadrian' => [
            'name' => 'Hadrian (Publius Aelius Hadrianus)',
            'reign_start' => 117,
            'reign_end' => 138,
            'tr_p_start' => 117, // 11 Ağustos 117
            'consulates' => [
                1 => 108,
                2 => 118,
                3 => 119, // 119'dan sonra bir daha konsüllük almadı (ölümüne kadar COS III)
            ],
        ],
        'antoninus-pius' => [
            'name' => 'Antoninus Pius (Titus Aurelius Fulvus Antoninus)',
            'reign_start' => 138,
            'reign_end' => 161,
            'tr_p_start' => 138,
            'consulates' => [1 => 120, 2 => 139, 3 => 140, 4 => 145],
        ],
        'marcus-aurelius' => [
            'name' => 'Marcus Aurelius (Marcus Aurelius Antoninus)',
            'reign_start' => 161,
            'reign_end' => 180,
            'tr_p_start' => 147, // Sezar olarak TR P I aldı
            'consulates' => [1 => 140, 2 => 145, 3 => 161],
        ],
        'commodus' => [
            'name' => 'Commodus (Marcus Aurelius Commodus Antoninus)',
            'reign_start' => 177,
            'reign_end' => 192,
            'tr_p_start' => 177,
            'consulates' => [1 => 177, 2 => 179, 3 => 181, 4 => 183, 5 => 186, 6 => 190, 7 => 192],
        ],
        'septimius-severus' => [
            'name' => 'Septimius Severus (Lucius Septimius Severus)',
            'reign_start' => 193,
            'reign_end' => 211,
            'tr_p_start' => 193,
            'consulates' => [1 => 190, 2 => 194, 3 => 202],
        ],
        'caracalla' => [
            'name' => 'Caracalla (Marcus Aurelius Severus Antoninus)',
            'reign_start' => 198,
            'reign_end' => 217,
            'tr_p_start' => 198,
            'consulates' => [1 => 202, 2 => 205, 3 => 208, 4 => 213],
        ],
    ];

    /**
     * Osmanlı Sultanları Cülus (Tahta Çıkış) Hicri Yıl Tablosu.
     */
    protected array $ottomanSultans = [
        'osman-gazi' => ['name' => 'Osman Gazi', 'accession_hijri' => 699, 'accession_gregorian' => 1299],
        'orhan-gazi' => ['name' => 'Orhan Gazi', 'accession_hijri' => 724, 'accession_gregorian' => 1324],
        'murad-i' => ['name' => 'I. Murad (Hüdavendigar)', 'accession_hijri' => 761, 'accession_gregorian' => 1362],
        'bayezid-i' => ['name' => 'I. Bayezid (Yıldırım)', 'accession_hijri' => 791, 'accession_gregorian' => 1389],
        'mehmed-i' => ['name' => 'I. Mehmed (Çelebi)', 'accession_hijri' => 816, 'accession_gregorian' => 1413],
        'murad-ii' => ['name' => 'II. Murad', 'accession_hijri' => 824, 'accession_gregorian' => 1421],
        'mehmed-ii' => ['name' => 'II. Mehmed (Fatih Sultan)', 'accession_hijri' => 855, 'accession_gregorian' => 1451],
        'bayezid-ii' => ['name' => 'II. Bayezid', 'accession_hijri' => 886, 'accession_gregorian' => 1481],
        'selim-i' => ['name' => 'I. Selim (Yavuz)', 'accession_hijri' => 918, 'accession_gregorian' => 1512],
        'suleyman-i' => ['name' => 'I. Süleyman (Kanuni)', 'accession_hijri' => 926, 'accession_gregorian' => 1520],
        'selim-ii' => ['name' => 'II. Selim', 'accession_hijri' => 974, 'accession_gregorian' => 1566],
        'murad-iii' => ['name' => 'III. Murad', 'accession_hijri' => 982, 'accession_gregorian' => 1574],
        'ahmed-i' => ['name' => 'I. Ahmed', 'accession_hijri' => 1012, 'accession_gregorian' => 1603],
        'murad-iv' => ['name' => 'IV. Murad', 'accession_hijri' => 1032, 'accession_gregorian' => 1623],
        'ahmed-iii' => ['name' => 'III. Ahmed (Lale Devri)', 'accession_hijri' => 1115, 'accession_gregorian' => 1703],
        'selim-iii' => ['name' => 'III. Selim', 'accession_hijri' => 1203, 'accession_gregorian' => 1789],
        'mahmud-ii' => ['name' => 'II. Mahmud', 'accession_hijri' => 1223, 'accession_gregorian' => 1808],
        'abdulmecid' => ['name' => 'Abdülmecid', 'accession_hijri' => 1255, 'accession_gregorian' => 1839],
        'abdulaziz' => ['name' => 'Abdülaziz', 'accession_hijri' => 1277, 'accession_gregorian' => 1861],
        'abdulhamid-ii' => ['name' => 'II. Abdülhamid', 'accession_hijri' => 1293, 'accession_gregorian' => 1876],
        'mehmed-v' => ['name' => 'V. Mehmed (Reşad)', 'accession_hijri' => 1327, 'accession_gregorian' => 1909],
        'mehmed-vi' => ['name' => 'VI. Mehmed (Vahideddin)', 'accession_hijri' => 1336, 'accession_gregorian' => 1918],
    ];

    /**
     * Grek Alfabesi Rakam Karşılıkları (Milesian Numeral System).
     */
    protected array $greekNumeralValues = [
        'Α' => 1, 'Β' => 2, 'Γ' => 3, 'Δ' => 4, 'Ε' => 5, 'Ϝ' => 6, 'Ϛ' => 6, 'S' => 6, 'Ζ' => 7, 'Η' => 8, 'Θ' => 9,
        'Ι' => 10, 'Κ' => 20, 'Λ' => 30, 'Μ' => 40, 'Ν' => 50, 'Ξ' => 60, 'Ο' => 70, 'Π' => 80, 'Ϙ' => 90,
        'Ρ' => 100, 'Σ' => 200, 'Τ' => 300, 'Υ' => 400, 'Φ' => 500, 'Χ' => 600, 'Ψ' => 700, 'Ω' => 800, 'Ϡ' => 900,
    ];

    /**
     * 1. ROMA MODÜLÜ:
     * Hükümdar, TR P (Tribunicia Potestas) ve COS (Consul) unvanlarını kesiştirerek
     * kesin Miladi yıl ve mevsim aralığını hesaplar.
     */
    public function convertRoman(string $rulerSlug, ?int $trP = null, ?int $cos = null, ?string $rawTitles = null): array
    {
        $slug = strtolower(trim($rulerSlug));
        $emperor = $this->romanEmperors[$slug] ?? null;

        if (!$emperor) {
            // Eğer doğrudan eşleşmediyse trajan'ı varsayılan referans alabiliriz veya hata döneriz
            return [
                'success' => false,
                'error' => "Bilinmeyen Roma imparatoru: '{$rulerSlug}'. Desteklenen imparatorlar: " . implode(', ', array_keys($this->romanEmperors)),
            ];
        }

        // Eğer ham metin girildiyse (Örn: "TR P IX COS IIII" veya "TR P XVII COS VI") regex ile ayrıştır
        if ($rawTitles) {
            if (!$trP && preg_match('/TR\s*P\s*(?:OT)?\s*([IVXLCDM]+|\d+)/i', $rawTitles, $mTr)) {
                $trP = is_numeric($mTr[1]) ? (int)$mTr[1] : $this->parseRomanNumeral($mTr[1]);
            }
            if (!$cos && preg_match('/COS\s*([IVXLCDM]+|\d+)/i', $rawTitles, $mCos)) {
                $cos = is_numeric($mCos[1]) ? (int)$mCos[1] : $this->parseRomanNumeral($mCos[1]);
            }
        }

        $minYear = $emperor['reign_start'];
        $maxYear = $emperor['reign_end'];
        $datingSteps = [];

        // 1. TR P Hesaplaması
        $trPStartYear = null;
        $trPEndYear = null;
        if ($trP !== null && $trP > 0) {
            $base = $emperor['tr_p_start'] ?? $emperor['reign_start'];
            // Roma tribünlük yetkisi çoğunlukla 10 Aralık'ta yenilenirdi
            $trPStartYear = $base + ($trP - 1);
            $trPEndYear = $trPStartYear + 1;

            $minYear = $trPStartYear;
            $maxYear = $trPEndYear;

            $datingSteps[] = "TR P {$trP} (Tribünlük Yetkisi): M.S. {$trPStartYear} kışından M.S. {$trPEndYear} kışına kadar geçerlidir.";
        }

        // 2. COS Hesaplaması
        if ($cos !== null && $cos > 0) {
            $consulates = $emperor['consulates'] ?? [];
            if (isset($consulates[$cos])) {
                $cosYear = $consulates[$cos];
                // Bir sonraki konsüllüğe kadar veya saltanat sonuna kadar geçerli
                $nextCosYear = $emperor['reign_end'];
                foreach ($consulates as $nextNum => $year) {
                    if ($nextNum > $cos) {
                        $nextCosYear = $year;
                        break;
                    }
                }

                if ($trP !== null) {
                    // TR P ile kesiştir
                    $intersectStart = max($minYear, $cosYear);
                    $intersectEnd = min($maxYear, $nextCosYear);
                    if ($intersectStart <= $intersectEnd) {
                        $minYear = $intersectStart;
                        $maxYear = $intersectEnd;
                    }
                } else {
                    $minYear = $cosYear;
                    $maxYear = $nextCosYear;
                }

                $datingSteps[] = "COS {$cos} (Konsüllük): M.S. {$cosYear} yılında başlanmış ve {$nextCosYear} yılına kadar taşınmıştır.";
            }
        }

        // Kesin Tarih ve Mevsim İfadesi
        if ($minYear === $maxYear) {
            $formattedDate = "M.S. {$minYear}";
            $exactSeason = "M.S. {$minYear}";
            $verdict = "Bu sikke tam olarak M.S. {$minYear} yılında basılmıştır.";
        } elseif ($maxYear - $minYear === 1) {
            $formattedDate = "M.S. {$minYear} - {$maxYear}";
            $exactSeason = "M.S. {$minYear}-{$maxYear} kışında";
            $verdict = "Bu sikke tam olarak M.S. {$minYear}-{$maxYear} kışında basılmıştır.";
        } else {
            $formattedDate = "M.S. {$minYear} - {$maxYear}";
            $exactSeason = "M.S. {$minYear} ile {$maxYear} yılları arasında";
            $verdict = "Bu sikke tam olarak M.S. {$minYear}-{$maxYear} döneminde basılmıştır.";
        }

        return [
            'success' => true,
            'system' => 'roman',
            'ruler' => $emperor['name'],
            'tr_p' => $trP,
            'cos' => $cos,
            'gregorian_start' => $minYear,
            'gregorian_end' => $maxYear,
            'formatted_date' => $formattedDate,
            'exact_season' => $exactSeason,
            'verdict' => $verdict,
            'explanation' => "{$emperor['name']} biyografisi ve Roma unvan kronolojisi eşleştirildiğinde; " . implode(' ', $datingSteps) . " {$verdict}",
        ];
    }

    /**
     * 2. GREK / SELEUKOS MODÜLÜ:
     * A.S. (Anno Seleuci) yılını veya Grek harfli rakamını Miladi yıla çevirir.
     * Referans: M.Ö. 312 Sonbaharı (Dios ayı).
     */
    public function convertSeleucid(int|string $inputYear): array
    {
        $raw = trim((string)$inputYear);
        $asYear = is_numeric($raw) ? (int)$raw : $this->parseGreekNumeral($raw);

        if ($asYear <= 0) {
            return [
                'success' => false,
                'error' => "Geçersiz Seleukos yılı: '{$inputYear}'. Lütfen 1 ile 600 arasında bir sayı veya Grek harfi girin (Örn: 112 veya ΒΙΡ).",
            ];
        }

        // Seleukos Çağı M.Ö. 312'de başlar.
        // Yıl 1 A.S. = M.Ö. 312 / 311
        if ($asYear <= 312) {
            $bcStart = 312 - $asYear + 1;
            $bcEnd = 312 - $asYear;
            if ($bcEnd === 0) {
                $formattedDate = "M.Ö. {$bcStart} / M.S. 1";
                $startYear = -$bcStart;
                $endYear = 1;
            } else {
                $formattedDate = "M.Ö. {$bcStart} - {$bcEnd}";
                $startYear = -$bcStart;
                $endYear = -$bcEnd;
            }
            $era = 'M.Ö.';
        } else {
            // M.S. dönemine geçiş
            $adStart = $asYear - 312;
            $adEnd = $asYear - 311;
            $formattedDate = "M.S. {$adStart} - {$adEnd}";
            $startYear = $adStart;
            $endYear = $adEnd;
            $era = 'M.S.';
        }

        $verdict = "Bu sikke tam olarak {$formattedDate} döneminde basılmıştır.";

        return [
            'success' => true,
            'system' => 'seleucid',
            'as_year' => $asYear,
            'input_raw' => $raw,
            'gregorian_start' => $startYear,
            'gregorian_end' => $endYear,
            'formatted_date' => $formattedDate,
            'era' => $era,
            'verdict' => $verdict,
            'explanation' => "Seleukos Takvimi (Anno Seleuci - A.S.) I. Seleukos Nikator'un Babil'e giriş tarihi olan M.Ö. 312 sonbaharında başlar. Sikkede okunan {$asYear} A.S. yılı, modern Miladi takvimde kesin olarak {$formattedDate} dönemine denk gelmektedir.",
        ];
    }

    /**
     * 3. İSLAMİ / OSMANLI MODÜLÜ:
     * Cülus Yılı (Tahta çıkış hicri yılı) + Sene-i Cülus (Saltanat yılı)
     * hesabıyla kesin Miladi yılı bulur.
     */
    public function convertIslamicOttoman(int $accessionHijri, int $regnalYear, ?string $sultanSlug = null): array
    {
        if ($accessionHijri < 1 || $accessionHijri > 1500) {
            return [
                'success' => false,
                'error' => "Geçersiz Cülus Hicri Yılı: {$accessionHijri}. Genellikle 699 ile 1342 arasında olmalıdır.",
            ];
        }

        if ($regnalYear < 1 || $regnalYear > 60) {
            return [
                'success' => false,
                'error' => "Geçersiz Sene-i Cülus (Saltanat Yılı): {$regnalYear}. 1 ile 60 arasında bir rakam giriniz.",
            ];
        }

        // Sikkenin basıldığı gerçek Hicri yıl:
        // Cülus yılı 1. sene sayıldığından: H = Cülus + (Sene - 1)
        $coinHijriYear = $accessionHijri + ($regnalYear - 1);

        // Hicri Yıldan Miladi Yıla Hassas Dönüşüm (Ay yılı = 354.36 gün, Güneş yılı = 365.24 gün)
        // Miladi Yıl ≈ Hicri Yıl - (Hicri Yıl / 33) + 622
        $approxGregorian = $coinHijriYear - ($coinHijriYear / 33.7) + 621.57;
        $gregorianStart = (int) floor($approxGregorian);
        $gregorianEnd = $gregorianStart + 1;

        $sultanInfo = $sultanSlug && isset($this->ottomanSultans[$sultanSlug])
            ? $this->ottomanSultans[$sultanSlug]['name']
            : null;

        $formattedDate = "Miladi {$gregorianStart} - {$gregorianEnd} (Hicri {$coinHijriYear})";
        $verdict = "Bu sikke tam olarak Miladi {$gregorianEnd} (Hicri {$coinHijriYear}) yılında basılmıştır.";

        return [
            'success' => true,
            'system' => 'islamic_ottoman',
            'sultan' => $sultanInfo,
            'accession_hijri' => $accessionHijri,
            'regnal_year' => $regnalYear,
            'calculated_hijri' => $coinHijriYear,
            'gregorian_start' => $gregorianStart,
            'gregorian_end' => $gregorianEnd,
            'formatted_date' => $formattedDate,
            'verdict' => $verdict,
            'explanation' => ($sultanInfo ? "{$sultanInfo} " : "") . "Cülus Yılı {$accessionHijri} ve {$regnalYear}. Sene-i Cülus hesaplandığında, sikke Hicri {$coinHijriYear} yılında basılmıştır. Ay takvimi ile Güneş takvimi arasındaki fark dikkate alındığında Miladi {$gregorianStart}-{$gregorianEnd} (özellikle {$gregorianEnd}) yılına tekabül eder.",
        ];
    }

    /**
     * Tüm sistemleri tek bir giriş noktasından çalıştıran akıllı çözücü.
     */
    public function calculate(array $params): array
    {
        $system = strtolower($params['system'] ?? 'roman');

        return match ($system) {
            'roman', 'roma' => $this->convertRoman(
                rulerSlug: $params['ruler'] ?? 'trajan',
                trP: !empty($params['tr_p']) ? (int)$params['tr_p'] : null,
                cos: !empty($params['cos']) ? (int)$params['cos'] : null,
                rawTitles: $params['raw_titles'] ?? null,
            ),
            'seleucid', 'greek', 'grek' => $this->convertSeleucid(
                inputYear: $params['as_year'] ?? $params['year'] ?? 112,
            ),
            'islamic', 'ottoman', 'osmanli' => $this->convertIslamicOttoman(
                accessionHijri: (int)($params['accession_hijri'] ?? 1223),
                regnalYear: (int)($params['regnal_year'] ?? 15),
                sultanSlug: $params['sultan'] ?? null,
            ),
            default => [
                'success' => false,
                'error' => "Desteklenmeyen takvim sistemi: {$system}. Desteklenenler: roman, seleucid, islamic",
            ],
        };
    }

    /**
     * Roma Rakamlarını (IV, IX, IIII vb.) Tam Sayıya Çevirici.
     */
    public function parseRomanNumeral(string $roman): int
    {
        $roman = strtoupper(trim($roman));
        // Antik sikkelerdeki IIII veya VIIII gibi arkaik formları standartlaştır
        $roman = str_replace(['IIII', 'VIIII', 'XXXX'], ['IV', 'IX', 'XL'], $roman);

        $map = ['M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50, 'X' => 10, 'V' => 5, 'I' => 1];
        $total = 0;
        $prev = 0;

        for ($i = strlen($roman) - 1; $i >= 0; $i--) {
            $char = $roman[$i];
            $val = $map[$char] ?? 0;
            if ($val < $prev) {
                $total -= $val;
            } else {
                $total += $val;
                $prev = $val;
            }
        }

        return $total;
    }

    /**
     * Grek Harfli Rakamları (Milesian System: ΒΙΡ = 112, ΣΞΓ = 263 vb.) Tam Sayıya Çevirici.
     */
    public function parseGreekNumeral(string $greek): int
    {
        $greek = mb_strtoupper(trim($greek), 'UTF-8');
        // Vurgu ve apostrof işaretlerini temizle (ΒΙΡ' -> ΒΙΡ)
        $greek = str_replace(["'", "’", "ʹ", " "], '', $greek);

        $total = 0;
        $len = mb_strlen($greek, 'UTF-8');

        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($greek, $i, 1, 'UTF-8');
            if (isset($this->greekNumeralValues[$char])) {
                $total += $this->greekNumeralValues[$char];
            }
        }

        return $total;
    }

    /**
     * Desteklenen Roma İmparatorları Listesi (Form ve Seçiciler İçin).
     */
    public function getSupportedRomanEmperors(): array
    {
        $list = [];
        foreach ($this->romanEmperors as $slug => $data) {
            $list[] = [
                'slug' => $slug,
                'name' => $data['name'],
                'reign' => "M.S. {$data['reign_start']} - {$data['reign_end']}",
                'max_cos' => count($data['consulates'] ?? []),
            ];
        }
        return $list;
    }

    /**
     * Desteklenen Osmanlı Sultanları Listesi (Form ve Seçiciler İçin).
     */
    public function getSupportedOttomanSultans(): array
    {
        $list = [];
        foreach ($this->ottomanSultans as $slug => $data) {
            $list[] = [
                'slug' => $slug,
                'name' => $data['name'],
                'accession_hijri' => $data['accession_hijri'],
                'accession_gregorian' => $data['accession_gregorian'],
            ];
        }
        return $list;
    }
}
