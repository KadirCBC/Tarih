<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * "Haftanın Sikkesi" E-posta Bültenine kayıt ucu.
     */
    public function subscribe(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'locale' => ['nullable', 'string', 'in:tr,en,de'],
            'source' => ['nullable', 'string', 'max:50'],
        ], [
            'email.required' => 'Lütfen geçerli bir e-posta adresi girin.',
            'email.email' => 'Girdiğiniz e-posta adresi formatı geçersizdir.',
        ]);

        $email = strtolower(trim($validated['email']));
        $locale = $validated['locale'] ?? app()->getLocale() ?? 'tr';
        $source = $validated['source'] ?? 'website';

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if (!$subscriber->is_active) {
                $subscriber->update([
                    'is_active' => true,
                    'unsubscribed_at' => null,
                    'locale' => $locale,
                ]);
            }
        } else {
            NewsletterSubscriber::create([
                'email' => $email,
                'locale' => $locale,
                'source' => $source,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);
        }

        Log::info("Haftanın Sikkesi bültenine yeni abone kaydedildi: {$email}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tebrikler! "Haftanın Sikkesi" nümizmatik e-posta bültenine başarıyla kaydoldunuz.',
            ]);
        }

        return back()->with('success', 'Tebrikler! "Haftanın Sikkesi" nümizmatik e-posta bültenine başarıyla kaydoldunuz.');
    }
}

