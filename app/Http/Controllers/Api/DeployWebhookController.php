<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeployWebhookController extends Controller
{
    /**
     * Paylaşımlı hostinglerde (shell_exec / SSH kapalıysa) web tabanlı
     * güvenli bakım modu ve deploy zincirini çalıştırır.
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Güvenlik Belirteci (Token) Doğrulaması
        $configuredToken = config('app.deploy_webhook_secret', env('DEPLOY_WEBHOOK_SECRET'));
        $receivedToken = $request->header('X-Deploy-Token') ?? $request->query('token');

        if (empty($configuredToken) || $receivedToken !== $configuredToken) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim: Geçersiz veya eksik deploy token.',
            ], 403);
        }

        $logs = [];

        try {
            // 2. Bakım Modunu Güvenli Şekilde Aç
            Artisan::call('down', [
                '--retry' => 30,
                '--secret' => 'deploy-bypass',
            ]);
            $logs[] = 'Bakım modu aktif edildi: ' . trim(Artisan::output());

            // 3. Veritabanı Migrasyonları
            Artisan::call('migrate', ['--force' => true]);
            $logs[] = 'Migrate: ' . trim(Artisan::output());

            // 4. Önbellekleri Temizle ve Isıt
            Artisan::call('config:cache');
            $logs[] = 'Config cache: ' . trim(Artisan::output());

            Artisan::call('route:cache');
            $logs[] = 'Route cache: ' . trim(Artisan::output());

            Artisan::call('view:cache');
            $logs[] = 'View cache: ' . trim(Artisan::output());

            // 5. Filament & Livewire Varsa Optimize Et
            if (array_key_exists('filament:optimize', Artisan::all())) {
                Artisan::call('filament:optimize');
                $logs[] = 'Filament optimize: ' . trim(Artisan::output());
            }

            // 6. Storage Link Kontrolü
            Artisan::call('storage:link');
            $logs[] = 'Storage link: ' . trim(Artisan::output());

            // 7. Bakım Modunu Kapat
            Artisan::call('up');
            $logs[] = 'Bakım modu kapatıldı: ' . trim(Artisan::output());

            Log::info('Web tabanlı deploy webhook başarıyla çalıştırıldı.', ['logs' => $logs]);

            return response()->json([
                'success' => true,
                'message' => 'Deployment başarıyla tamamlandı.',
                'timestamp' => now()->toIso8601String(),
                'steps' => $logs,
            ]);

        } catch (\Throwable $e) {
            // Hata olsa dahi siteyi bakım modunda bırakma
            Artisan::call('up');
            Log::error('Web deploy webhook hatası: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Deployment sırasında hata oluştu, bakım modu kapatıldı.',
                'error' => $e->getMessage(),
                'partial_steps' => $logs,
            ], 500);
        }
    }
}

