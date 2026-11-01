<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoApiController extends Controller
{
    /**
     * Tarihsel imparatorluk sınırları ve antik ticaret yolları için hafif GeoJSON katmanları.
     * Örn: M.S. 117 Trajan dönemi Roma İmparatorluğu'nun en geniş sınırları ve Via Egnatia / Deniz rotaları.
     */
    public function boundaries(Request $request): JsonResponse
    {
        $layer = $request->query('layer', 'all');

        $features = [];

        // 1. M.S. 117 Trajan Dönemi Roma İmparatorluğu Maksimum Sınırları (Hafif GeoJSON Poligonu)
        if (in_array($layer, ['all', 'trajan_117', 'roman_empire'], true)) {
            $features[] = [
                'type' => 'Feature',
                'id' => 'trajan_117_border',
                'properties' => [
                    'name' => 'M.S. 117 Trajan Dönemi Roma İmparatorluğu Sınırları',
                    'period' => 'M.S. 98 - 117',
                    'ruler' => 'Marcus Ulpius Traianus (Optimus Princeps)',
                    'description' => 'Britanya\'dan Mezopotamya ve Basra Körfezi\'ne, Cebelitarık\'tan Hazar Denizi\'ne Roma\'nın tarihteki en geniş sınırları.',
                    'stroke_color' => '#dc2626',
                    'fill_color' => '#b91c1c',
                    'fill_opacity' => 0.12,
                    'stroke_width' => 2.5,
                ],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [
                            // Britanya & İskoçya sınırı
                            [-3.15, 55.05],
                            [-1.50, 55.00],
                            [0.50, 52.80],
                            [1.75, 51.50],
                            // Manş & Ren Ağzı (Batı Cermanya Limes)
                            [4.20, 52.10],
                            [6.10, 51.50],
                            [7.50, 50.40],
                            [8.60, 50.00],
                            // Yukarı Ren - Agri Decumates & Tuna Sınırı (Limes Germanicus)
                            [9.80, 48.90],
                            [12.10, 49.00],
                            [16.50, 48.10],
                            [19.00, 47.50],
                            // Daçya (Romanya - Trajan'ın fethettiği topraklar)
                            [22.50, 47.80],
                            [25.00, 47.50],
                            [27.50, 46.00],
                            [29.50, 45.20],
                            // Karadeniz Kuzeyi & Kafkaslar (Moesia & Pontus)
                            [30.50, 46.60],
                            [35.00, 45.20],
                            [39.50, 44.50],
                            [41.60, 42.00],
                            // Ermenistan & Mezopotamya (Fırat - Dicle & Basra Körfezi sınırı)
                            [44.00, 40.00],
                            [47.50, 36.00],
                            [48.50, 31.00],
                            [47.00, 30.00],
                            // Arabistan Petraea & Sina Yarımadası
                            [40.00, 30.50],
                            [36.00, 29.50],
                            [34.50, 27.80],
                            // Mısır (Nil Vadisi & Aswan Limes)
                            [32.90, 24.10],
                            [29.50, 26.50],
                            [25.00, 30.50],
                            // Sirenayka & Kuzey Afrika (Fossum Africae)
                            [20.00, 31.50],
                            [15.00, 31.00],
                            [10.50, 33.50],
                            [6.00, 34.50],
                            [-1.00, 34.00],
                            // Mauretania Tingitana & Atlas Sınırı
                            [-5.80, 35.80],
                            [-9.50, 37.00],
                            // İber Yarımadası Atlantik Kıyısı (Lusitania & Hispania)
                            [-9.20, 38.70],
                            [-8.80, 42.00],
                            [-9.00, 43.50],
                            [-5.00, 43.60],
                            // Galya Atlantik Kıyısı
                            [-1.50, 46.00],
                            [-4.50, 48.30],
                            [-1.00, 49.80],
                            [-3.15, 55.05], // Döngü kapanışı
                        ],
                    ],
                ],
            ];
        }

        // 2. Antik Roma ve Akdeniz Ticaret & Askeri Rotaları
        if (in_array($layer, ['all', 'trade_routes', 'roads'], true)) {
            // Via Appia (Roma -> Brundisium)
            $features[] = [
                'type' => 'Feature',
                'id' => 'via_appia',
                'properties' => [
                    'name' => 'Via Appia (Regina Viarum)',
                    'category' => 'Kara Ticaret ve Askeri Yolu',
                    'route' => 'Roma - Capua - Beneventum - Tarentum - Brundisium',
                    'stroke_color' => '#d97706',
                    'stroke_width' => 3,
                    'dash_array' => '4, 4',
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [12.492, 41.890], // Roma
                        [13.805, 41.487], // Tarracina / Terracina
                        [14.250, 41.083], // Capua
                        [14.780, 41.130], // Beneventum
                        [16.900, 40.780], // Venusia
                        [17.240, 40.470], // Tarentum
                        [17.940, 40.630], // Brundisium (Brindisi)
                    ],
                ],
            ];

            // Via Egnatia (Dyrrhachium -> Thessalonica -> Konstantinopolis)
            $features[] = [
                'type' => 'Feature',
                'id' => 'via_egnatia',
                'properties' => [
                    'name' => 'Via Egnatia (Balkanları Birleştiren Ana Arter)',
                    'category' => 'Kara Ticaret ve Askeri Yolu',
                    'route' => 'Dyrrhachium (Dıraç) - Thessalonica (Selanik) - Philippi - Byzantium (İstanbul)',
                    'stroke_color' => '#d97706',
                    'stroke_width' => 3.5,
                    'dash_array' => '6, 4',
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [19.450, 41.320], // Dyrrhachium
                        [20.800, 41.110], // Lychnidos (Ohrid)
                        [21.340, 41.030], // Heraclea Lyncestis (Bitola)
                        [22.940, 40.640], // Thessalonica (Selanik)
                        [23.840, 40.820], // Amphipolis
                        [24.280, 41.010], // Philippi
                        [25.400, 41.050], // Traianopolis
                        [26.380, 40.850], // Cypsela (İpsala)
                        [27.500, 40.980], // Rhaedestus (Tekirdağ)
                        [28.978, 41.008], // Byzantium / Konstantinopolis
                    ],
                ],
            ];

            // Akdeniz Deniz Ticareti ve İskenderiye-Roma Buğday Rotası
            $features[] = [
                'type' => 'Feature',
                'id' => 'maritime_grain_route',
                'properties' => [
                    'name' => 'Akdeniz İmparatorluk Deniz Ticaret Rotası (Classis Alexandrina)',
                    'category' => 'Deniz Ticaret Rotası',
                    'route' => 'İskenderiye - Rodos - Efes - Korint - Messina - Ostia / Roma',
                    'stroke_color' => '#0284c7',
                    'stroke_width' => 2.5,
                    'dash_array' => '8, 6',
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [29.918, 31.200], // İskenderiye (Alexandria)
                        [32.500, 34.000], // Kıbrıs açıkları
                        [28.220, 36.440], // Rodos
                        [27.341, 37.941], // Efes (Ephesos)
                        [25.270, 37.400], // Delos
                        [22.930, 37.930], // Korint
                        [15.550, 38.190], // Messina Boğazı
                        [14.260, 40.850], // Puteoli (Napoli)
                        [12.280, 41.750], // Portus / Ostia (Roma Limanı)
                    ],
                ],
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}

