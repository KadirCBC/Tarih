<?php

namespace App\Jobs;

use App\Models\Coin;
use App\Models\CoinImage;
use App\Enums\ImageType;
use App\Services\ImageOptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessCoinImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 180;

    /**
     * Create a new job instance.
     *
     * @param int $coinId
     * @param string $tempFilePath  Path in storage/app/temp or uploaded file path
     * @param ImageType $imageType
     * @param bool $isPrimary
     */
    public function __construct(
        public int $coinId,
        public string $tempFilePath,
        public ImageType $imageType = ImageType::OBVERSE,
        public bool $isPrimary = false
    ) {}

    /**
     * Execute the job.
     * cPanel / Plesk friendly: isolates GD memory usage and triggers garbage collection.
     */
    public function handle(ImageOptimizationService $optimizer): void
    {
        $coin = Coin::find($this->coinId);

        if (!$coin) {
            Log::warning("ProcessCoinImageJob: Sikke bulunamadı (ID: {$this->coinId}). İşlem iptal edildi.");
            $this->cleanTempFile();
            return;
        }

        if (!file_exists($this->tempFilePath)) {
            Log::error("ProcessCoinImageJob: Geçici görsel dosyası bulunamadı: {$this->tempFilePath}");
            return;
        }

        try {
            // Memory optimization: Set temporary execution and memory bounds safely
            @ini_set('memory_limit', '256M');

            // Optimize to WebP max 1600px width
            $optimizedRelativePath = $optimizer->optimizeToWebp(
                $this->tempFilePath,
                'coins',
                1600,
                82
            );

            // Create coin image record in database
            CoinImage::create([
                'coin_id' => $coin->id,
                'image_path' => $optimizedRelativePath,
                'image_type' => $this->imageType,
                'is_primary' => $this->isPrimary,
                'sort_order' => $this->isPrimary ? 0 : 1,
            ]);

            Log::info("ProcessCoinImageJob: Sikke görseli optimize edilip kaydedildi. Coin ID: {$coin->id}, Path: {$optimizedRelativePath}");

            // Clean up the raw temp file to save disk space
            $this->cleanTempFile();

            // Force PHP garbage collection on low-memory shared hosts
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

        } catch (Throwable $e) {
            Log::error("ProcessCoinImageJob Hatası [Coin ID: {$this->coinId}]: " . $e->getMessage(), [
                'file' => $this->tempFilePath,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Clean up raw temporary upload.
     */
    protected function cleanTempFile(): void
    {
        if (file_exists($this->tempFilePath)) {
            @unlink($this->tempFilePath);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical("ProcessCoinImageJob kalıcı olarak başarısız oldu [Coin ID: {$this->coinId}]: {$exception->getMessage()}");
        $this->cleanTempFile();
    }
}

