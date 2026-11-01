<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckScanQuota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class QuotaMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Test amaçlı korumalı test rotası tanımla
        Route::middleware([CheckScanQuota::class])->any('/test-scan-quota-endpoint', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Tarama başarılı',
            ]);
        });
    }

    /**
     * Free kullanıcı günlük 3 tarama limitine ulaştığında HTTP 429 ve QUOTA_EXCEEDED hatası almalıdır.
     */
    public function test_free_user_receives_http_429_when_exceeding_daily_limit(): void
    {
        $freeUser = User::create([
            'name' => 'Koleksiyoner Ali',
            'email' => 'ali@example.com',
            'password' => bcrypt('secret123'),
            'subscription_tier' => 'free',
        ]);

        $today = now()->format('Y-m-d');
        $cacheKey = "scan_quota:user:{$freeUser->id}:{$today}";

        // Kullanıcının kotasını 3 olarak simüle et
        Cache::put($cacheKey, 3, now()->endOfDay());

        $response = $this->actingAs($freeUser)
            ->postJson('/test-scan-quota-endpoint');

        $response->assertStatus(429)
            ->assertJson([
                'success' => false,
                'error_code' => 'QUOTA_EXCEEDED',
                'upgrade_required' => true,
                'daily_limit' => 3,
                'used_scans' => 3,
            ]);
    }

    /**
     * Pro abone kullanıcı limit aşımına takılmadan limitsiz geçiş yapabilmelidir.
     */
    public function test_pro_user_bypasses_daily_scan_quota_limit(): void
    {
        $proUser = User::create([
            'name' => 'Uzman Nümizmat Mehmet',
            'email' => 'mehmet@example.com',
            'password' => bcrypt('secret123'),
            'subscription_tier' => 'pro',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        $today = now()->format('Y-m-d');
        $cacheKey = "scan_quota:user:{$proUser->id}:{$today}";

        // Daha önceden 50 tarama yapmış olsa dahi
        Cache::put($cacheKey, 50, now()->endOfDay());

        $response = $this->actingAs($proUser)
            ->postJson('/test-scan-quota-endpoint');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tarama başarılı',
            ]);
    }

    /**
     * Giriş yapmamış misafir kullanıcı IP kotası dolduğunda HTTP 429 almalıdır.
     */
    public function test_guest_user_ip_blocked_after_3_scans(): void
    {
        $guestIp = '192.168.1.100';
        $today = now()->format('Y-m-d');
        $cacheKey = "scan_quota:ip:{$guestIp}:{$today}";

        Cache::put($cacheKey, 3, now()->endOfDay());

        $response = $this->withServerVariables(['REMOTE_ADDR' => $guestIp])
            ->postJson('/test-scan-quota-endpoint');

        $response->assertStatus(429)
            ->assertJsonPath('error_code', 'QUOTA_EXCEEDED');
    }
}

