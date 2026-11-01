<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionWebhookController extends Controller
{
    use ApiResponse;

    /**
     * Mobil İstemciden Gelen Satın Alma Makbuzunu Doğrulama (In-App Purchase Verification)
     * Apple StoreKit 2 veya Google Play Billing tamamlandıktan sonra mobil uygulama bu endpointi çağırır.
     */
    public function verifyPurchase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:apple,google,paddle,iyzico'],
            'product_id' => ['required', 'string'], // örn: 'numis_pro_monthly', 'numis_pro_yearly'
            'purchase_token' => ['nullable', 'string'], // Google Play Purchase Token
            'receipt_data' => ['nullable', 'string'],    // Apple JWS veya Base64 makbuz
            'transaction_id' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $provider = $validated['provider'];
        $productId = $validated['product_id'];

        $isValid = false;
        $expiresAt = null;
        $transactionId = $validated['transaction_id'] ?? 'TX-' . strtoupper(bin2hex(random_bytes(6)));

        // 1. Sağlayıcıya göre makbuz doğrulama
        if ($provider === 'apple') {
            $verification = $this->verifyAppleReceipt($validated['receipt_data'] ?? '', $productId);
            $isValid = $verification['is_valid'];
            $expiresAt = $verification['expires_at'];
            $transactionId = $verification['transaction_id'] ?? $transactionId;
        } elseif ($provider === 'google') {
            $verification = $this->verifyGooglePlayToken($validated['purchase_token'] ?? '', $productId);
            $isValid = $verification['is_valid'];
            $expiresAt = $verification['expires_at'];
            $transactionId = $verification['transaction_id'] ?? $transactionId;
        } else {
            // Paddle / Iyzico web ödemeleri
            $isValid = true;
            $expiresAt = str_contains($productId, 'yearly') ? now()->addYear() : now()->addMonth();
        }

        if (!$isValid) {
            return $this->error('Satın alma makbuzu doğrulanamadı. Lütfen mağaza hesabınızı kontrol edin.', 422);
        }

        // 2. Kullanıcıyı Nümizmat Pro Seviyesine Yükselt
        $user->update([
            'subscription_tier' => 'pro',
            'subscription_expires_at' => $expiresAt,
            'subscription_provider' => $provider,
            'subscription_id' => $transactionId,
        ]);

        Log::info("Kullanıcı #{$user->id} Pro üyeliğe yükseltildi ({$provider} - {$productId})");

        return $this->success([
            'subscription_tier' => 'pro',
            'is_pro' => true,
            'expires_at' => $expiresAt->toIso8601String(),
            'provider' => $provider,
            'unlocked_features' => [
                'unlimited_ai_scans' => true,
                'full_condition_valuations' => true, // VF, XF, UNC açık
                'unlimited_collection' => true,
                'pdf_certificate_export' => true,
                'macro_zoom_viewer' => true,
            ],
        ], 'Tebrikler! Nümizmat Pro aboneliğiniz başarıyla aktif edildi.');
    }

    /**
     * Apple StoreKit 2 Sunucu Bildirimleri (App Store Server Notifications V2)
     */
    public function handleAppleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Apple Webhook Alındı: ', $payload);

        // Apple JWS (JSON Web Signature) payload ayrıştırma simülasyonu
        // signedPayload -> decoded -> notificationType
        $notificationType = $payload['notificationType'] ?? 'SUBSCRIBED';
        $originalTransactionId = $payload['data']['signedTransactionInfo'] ?? null;

        // İlgili kullanıcıyı bul
        if ($originalTransactionId) {
            $user = User::where('subscription_id', $originalTransactionId)->first();
            if ($user) {
                match ($notificationType) {
                    'DID_RENEW', 'SUBSCRIBED' => $user->update([
                        'subscription_tier' => 'pro',
                        'subscription_expires_at' => now()->addMonth(),
                    ]),
                    'EXPIRED', 'REVOKE' => $user->update([
                        'subscription_tier' => 'free',
                        'subscription_expires_at' => now(),
                    ]),
                    default => null,
                };
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Google Play Billing Sunucu Bildirimleri (Cloud Pub/Sub RTDN)
     */
    public function handleGoogleWebhook(Request $request): JsonResponse
    {
        $message = $request->input('message.data');
        if ($message) {
            $decoded = json_decode(base64_decode($message), true);
            Log::info('Google Play RTDN Bildirimi: ', $decoded ?: []);

            $subscriptionNotification = $decoded['subscriptionNotification'] ?? null;
            if ($subscriptionNotification) {
                $purchaseToken = $subscriptionNotification['purchaseToken'] ?? null;
                $notificationType = (int) ($subscriptionNotification['notificationType'] ?? 0);

                if ($purchaseToken) {
                    $user = User::where('subscription_id', $purchaseToken)->first();
                    if ($user) {
                        // 1 = RECOVERED, 2 = RENEWED, 3 = CANCELED, 13 = EXPIRED
                        if (in_array($notificationType, [1, 2])) {
                            $user->update(['subscription_tier' => 'pro', 'subscription_expires_at' => now()->addMonth()]);
                        } elseif (in_array($notificationType, [3, 13])) {
                            $user->update(['subscription_tier' => 'free', 'subscription_expires_at' => now()]);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Apple StoreKit 2 Makbuz Doğrulama Simülasyonu
     */
    protected function verifyAppleReceipt(string $receiptData, string $productId): array
    {
        // Apple Sandbox veya Production verifyReceipt / App Store Server API çağrısı
        $isYearly = str_contains($productId, 'yearly');
        return [
            'is_valid' => true,
            'expires_at' => $isYearly ? now()->addYear() : now()->addMonth(),
            'transaction_id' => 'APL-' . strtoupper(substr(md5($receiptData . microtime()), 0, 12)),
        ];
    }

    /**
     * Google Play Developer API Token Doğrulama Simülasyonu
     */
    protected function verifyGooglePlayToken(string $purchaseToken, string $productId): array
    {
        // Google Android Publisher API purchases.subscriptions.get çağrısı
        $isYearly = str_contains($productId, 'yearly');
        return [
            'is_valid' => true,
            'expires_at' => $isYearly ? now()->addYear() : now()->addMonth(),
            'transaction_id' => 'GGL-' . strtoupper(substr(md5($purchaseToken . microtime()), 0, 12)),
        ];
    }
}

