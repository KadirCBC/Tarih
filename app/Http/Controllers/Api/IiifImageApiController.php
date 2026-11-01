<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoinImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IiifImageApiController extends Controller
{
    /**
     * IIIF Image API 3.0 Standardı: info.json uç noktası.
     * OpenSeadragon ve uluslararası kütüphaneler (British Museum, ANS vb.) bu JSON ile derin yakınlaştırma yapar.
     * GET /api/iiif/image/{id}/info.json
     */
    public function info(int $id): JsonResponse
    {
        $image = CoinImage::with('coin')->find($id);

        if (!$image) {
            return response()->json([
                'error' => 'Görsel bulunamadı.',
            ], 404);
        }

        $baseUrl = url("/api/iiif/image/{$image->id}");
        
        // Genişlik ve yükseklik tahmini veya gerçek dosya okuma
        $width = 2400;
        $height = 2400;

        $path = $image->image_path;
        if (!str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            $fullPath = Storage::disk('public')->path($path);
            if (file_exists($fullPath) && function_exists('getimagesize')) {
                $dims = @getimagesize($fullPath);
                if ($dims) {
                    $width = $dims[0];
                    $height = $dims[1];
                }
            }
        }

        $iiifInfo = [
            '@context' => 'http://iiif.io/api/image/3/context.json',
            'id' => $baseUrl,
            'type' => 'ImageService3',
            'protocol' => 'http://iiif.io/api/image',
            'profile' => 'level2',
            'width' => $width,
            'height' => $height,
            'maxHeight' => $height,
            'maxWidth' => $width,
            'tiles' => [
                [
                    'width' => 512,
                    'height' => 512,
                    'scaleFactors' => [1, 2, 4, 8, 16],
                ],
            ],
            'sizes' => [
                ['width' => (int)($width / 8), 'height' => (int)($height / 8)],
                ['width' => (int)($width / 4), 'height' => (int)($height / 4)],
                ['width' => (int)($width / 2), 'height' => (int)($height / 2)],
                ['width' => $width, 'height' => $height],
            ],
            'preferredFormats' => ['webp', 'jpg', 'png'],
            'service' => [
                [
                    'id' => $baseUrl,
                    'type' => 'ImageService3',
                    'profile' => 'level2',
                ],
            ],
            'metadata' => [
                [
                    'label' => ['tr' => ['Sikke Başlığı']],
                    'value' => ['tr' => [$image->coin?->title ?? 'Antik Sikke']],
                ],
                [
                    'label' => ['tr' => ['Görsel Türü']],
                    'value' => ['tr' => [strtoupper($image->image_type?->value ?? (string)$image->image_type)]],
                ],
                [
                    'label' => ['tr' => ['Katalog Referansı']],
                    'value' => ['tr' => [$image->coin?->catalog_reference ?? '-']],
                ],
            ],
        ];

        return response()->json($iiifInfo, 200, [
            'Content-Type' => 'application/ld+json;profile="http://iiif.io/api/image/3/context.json"',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, Accept',
        ]);
    }

    /**
     * IIIF Image API 3.0: Görsel teslim uç noktası.
     * /api/iiif/image/{id}/{region}/{size}/{rotation}/{quality}.{format}
     */
    public function renderImage(int $id, string $region, string $size, string $rotation, string $quality, string $format)
    {
        $image = CoinImage::find($id);

        if (!$image) {
            return response()->json(['error' => 'Görsel bulunamadı.'], 404);
        }

        // Doğrudan optimize edilmiş görsel dosyasına yönlendir veya içeriği gönder
        return redirect()->to($image->url);
    }
}

