<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ImageOptimizationService
{
    /**
     * Varsayılan maksimum görsel genişliği (piksel).
     */
    protected int $defaultMaxWidth = 1600;

    /**
     * Varsayılan WebP sıkıştırma kalitesi (0-100).
     */
    protected int $defaultQuality = 85;

    /**
     * Yüklenen sikke fotoğrafını WebP formatına dönüştürür, 1600px ile sınırlandırır ve storage'a kaydeder.
     *
     * @param UploadedFile|string $file Yüklenen dosya veya dosya yolu / binary veri
     * @param string $folder Storage diskindeki hedef klasör (varsayılan: 'coins')
     * @param int|null $maxWidth Maksimum piksel genişliği (varsayılan: 1600)
     * @param int|null $quality WebP kalite oranı (varsayılan: 85)
     * @return string Kaydedilen görselin göreli depolama yolu (örn: 'coins/abc123xyz.webp')
     */
    public function optimizeAndStore(
        UploadedFile|string $file,
        string $folder = 'coins',
        ?int $maxWidth = null,
        ?int $quality = null
    ): string {
        $maxWidth = $maxWidth ?? $this->defaultMaxWidth;
        $quality = $quality ?? $this->defaultQuality;

        // 1. Dosya içeriğini binary olarak oku
        $binaryData = $this->resolveBinaryContent($file);

        if (empty($binaryData)) {
            throw new InvalidArgumentException('Geçersiz veya boş görsel verisi.');
        }

        // 2. PHP GD ile görsel kaynağını oluştur
        $image = @imagecreatefromstring($binaryData);
        if (!$image) {
            throw new RuntimeException('Görsel işlenemedi. Desteklenmeyen veya bozuk dosya biçimi.');
        }

        // Şeffaflık (alpha channel) ayarları (PNG/WebP için)
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // 3. Boyutlandırma: Genişlik 1600px'den büyükse en-boy oranını koruyarak küçült
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        if ($originalWidth > $maxWidth) {
            $newHeight = (int) round(($originalHeight / $originalWidth) * $maxWidth);
            $resizedImage = imagescale($image, $maxWidth, $newHeight, IMG_BILINEAR_FIXED);

            if ($resizedImage !== false) {
                imagedestroy($image);
                $image = $resizedImage;
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
        }

        // 4. Benzersiz dosya adı ve depolama yolu
        $filename = Str::uuid()->toString() . '.webp';
        $relativePath = trim($folder, '/') . '/' . $filename;
        $disk = Storage::disk('public');

        // Hedef dizinin varlığını güvenceye al
        $targetDirectory = dirname($disk->path($relativePath));
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $fullDestinationPath = $disk->path($relativePath);

        // 5. WebP olarak kaydet
        $saved = imagewebp($image, $fullDestinationPath, $quality);
        imagedestroy($image);

        if (!$saved || !file_exists($fullDestinationPath)) {
            throw new RuntimeException('WebP görseli diske yazılamadı.');
        }

        return $relativePath;
    }

    /**
     * Dosya veya path girdisinden ikili (binary) veriyi ayıklar.
     */
    protected function resolveBinaryContent(UploadedFile|string $file): string
    {
        if ($file instanceof UploadedFile) {
            return (string) file_get_contents($file->getRealPath());
        }

        if (is_file($file)) {
            return (string) file_get_contents($file);
        }

        // Base64 formatı kontrolü
        if (str_starts_with($file, 'data:image/') || preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', substr($file, 0, 100))) {
            $cleanBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $file);
            $decoded = base64_decode($cleanBase64, true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $file;
    }

    /**
     * Storage'dan sikke görselini siler.
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            return $disk->delete($path);
        }

        return false;
    }
}

