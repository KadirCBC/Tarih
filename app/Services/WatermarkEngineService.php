<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WatermarkEngineService
{
    /**
     * Orijinal dosyaya dokunmadan dinamik olarak yarı saydam nümizmatik
     * yasal filigranı basarak HTTP StreamedResponse döner.
     */
    public function renderWatermarkedImage(string $path): \Illuminate\Http\Response
    {
        $fullPath = null;

        if (Storage::disk('public')->exists($path)) {
            $fullPath = Storage::disk('public')->path($path);
        } elseif (file_exists($path)) {
            $fullPath = $path;
        } elseif (file_exists(public_path($path))) {
            $fullPath = public_path($path);
        }

        if (!$fullPath || !file_exists($fullPath)) {
            abort(404, 'Görsel bulunamadı.');
        }

        $imageInfo = @getimagesize($fullPath);
        if (!$imageInfo) {
            abort(415, 'Desteklenmeyen görsel formatı.');
        }

        $mime = $imageInfo['mime'];
        $srcImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($fullPath),
            'image/png' => @imagecreatefrompng($fullPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null,
            default => null,
        };

        if (!$srcImage) {
            // GD ile açılamazsa orijinal dosyayı doğrudan dön
            return response(file_get_contents($fullPath), 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $width = imagesx($srcImage);
        $height = imagesy($srcImage);

        // Alfa kanalı / Şeffaflık desteği
        imagealphablending($srcImage, true);
        imagesavealpha($srcImage, true);

        // 1. Alt Kısımda Yarı Saydam Koruma Şeridi
        $bannerHeight = max(36, (int)($height * 0.08));
        $bannerY = $height - $bannerHeight;

        // Yarı saydam koyu zemin (RGBA: 15, 23, 42, alpha ~ 55 / 127)
        $darkBanner = imagecolorallocatealpha($srcImage, 15, 23, 42, 45);
        imagefilledrectangle($srcImage, 0, $bannerY, $width, $height, $darkBanner);

        // Üst ince altın vurgu çizgisi
        $goldLine = imagecolorallocatealpha($srcImage, 245, 158, 11, 40);
        imageline($srcImage, 0, $bannerY, $width, $bannerY, $goldLine);

        // Metin Renkleri (Yarı saydam beyaz ve altın)
        $textColor = imagecolorallocatealpha($srcImage, 255, 255, 255, 15);
        $amberText = imagecolorallocatealpha($srcImage, 251, 191, 36, 15);

        // 2. Yasal Uyarı Metinleri (2863 Sayılı Kanun Uyarısı)
        $textLine1 = "AKADEMIK ARSIV REFERANSIDIR - ALIM SATIMI YAPILAMAZ";
        $textLine2 = "2863 Sayili Kultur Varliklari Kanunu Hukukuna Tabidir";

        // Dahili GD yazı tipi (Font 3 ve 2)
        $font1 = 3;
        $font2 = 2;

        $text1Width = imagefontwidth($font1) * strlen($textLine1);
        $text1Height = imagefontheight($font1);
        $text1X = max(10, (int)(($width - $text1Width) / 2));
        $text1Y = $bannerY + (int)(($bannerHeight / 2) - $text1Height) - 2;

        $text2Width = imagefontwidth($font2) * strlen($textLine2);
        $text2X = max(10, (int)(($width - $text2Width) / 2));
        $text2Y = $text1Y + $text1Height + 3;

        imagestring($srcImage, $font1, $text1X, $text1Y, $text1Line1 ?? $textLine1, $amberText);
        imagestring($srcImage, $font2, $text2X, $text2Y, $text2Line2 ?? $textLine2, $textColor);

        // 3. Merkezde Çapraz Yarı Saydam Mikro Filigran (Scraper'ların Kırpmasını Önler)
        $watermarkMicro = "TARIH NUMIZMATIK ARSIVI - REPRODÜKSIYON YASAKTIR";
        $microColor = imagecolorallocatealpha($srcImage, 255, 255, 255, 105); // Yüksek şeffaflık (~%85 saydam)
        
        $diagX = max(15, (int)($width * 0.15));
        $diagY = (int)($height * 0.45);
        imagestring($srcImage, 4, $diagX, $diagY, $watermarkMicro, $microColor);

        // Görseli bellekte WebP veya JPEG olarak çıktıla
        ob_start();
        if (function_exists('imagewebp')) {
            imagewebp($srcImage, null, 85);
            $outputMime = 'image/webp';
        } else {
            imagejpeg($srcImage, null, 85);
            $outputMime = 'image/jpeg';
        }
        $imageData = ob_get_clean();
        imagedestroy($srcImage);

        return response($imageData, 200, [
            'Content-Type' => $outputMime,
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
            'X-Watermark-Applied' => 'dynamic-academic-v1',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

