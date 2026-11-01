<?php

namespace App\Services;

use App\Models\Coin;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Http\Response;

class DailyCoinQuizService
{
    /**
     * Günün nümizmatik sorusunu döner. Yoksa katalogdan dinamik oluşturur.
     */
    public function getTodayChallenge(): array
    {
        $today = now()->toDateString();
        $question = QuizQuestion::where('is_daily_challenge', true)
            ->whereDate('challenge_date', $today)
            ->with('coin.primaryImage', 'coin.ruler', 'coin.period')
            ->first();

        if (!$question) {
            $question = $this->generateDailyQuestion($today);
        }

        $coin = $question->coin;

        return [
            'id' => $question->id,
            'challenge_date' => $today,
            'question_type' => $question->question_type,
            'question_text' => $question->question_text,
            'options' => $question->options_json,
            'image_url' => $coin?->primaryImage?->url ?? asset('images/coin-placeholder.webp'),
            'coin_hint' => [
                'period' => $coin?->period?->name ?? 'Antik Çağ',
                'denomination' => $coin?->denomination ?? 'Sikke',
                'metal' => $coin?->metal?->value ?? 'silver',
            ],
        ];
    }

    /**
     * Kullanıcı cevabını doğrular, puan ve rozetleri işletir.
     */
    public function answerChallenge(int $questionId, int $selectedOptionIndex, ?User $user = null): array
    {
        $question = QuizQuestion::with('coin.ruler', 'coin.period')->find($questionId);

        if (!$question) {
            return [
                'success' => false,
                'message' => 'Soru bulunamadı.',
            ];
        }

        $isCorrect = ($selectedOptionIndex === (int)$question->correct_option_index);
        $pointsEarned = $isCorrect ? 50 : 0;
        $newBadgeUnlocked = null;
        $currentStreak = 1;

        if ($user && $isCorrect) {
            // Puan ekle
            $user->addNumismaticPoints($pointsEarned);

            // Günlük Seri (Streak) Mantığı
            $streak = $user->streak()->firstOrCreate(['user_id' => $user->id]);
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            if ($streak->last_activity_date?->toDateString() === $yesterday) {
                $streak->increment('current_streak_days');
            } elseif ($streak->last_activity_date?->toDateString() !== $today) {
                $streak->current_streak_days = 1;
            }

            if ($streak->current_streak_days > $streak->longest_streak_days) {
                $streak->longest_streak_days = $streak->current_streak_days;
            }
            $streak->last_activity_date = now();
            $streak->save();
            $currentStreak = $streak->current_streak_days;

            // Rozet Kazanımı Kontrolleri
            if ($user->awardBadge('novice_collector')) {
                $newBadgeUnlocked = 'novice_collector';
            } elseif ($question->question_type === 'correct_translation' && $user->awardBadge('legend_reader')) {
                $newBadgeUnlocked = 'legend_reader';
            } elseif ($question->question_type === 'real_or_fake' && $user->awardBadge('keen_eye')) {
                $newBadgeUnlocked = 'keen_eye';
            } elseif ($question->coin?->period?->slug === 'roma-imparatorlugu' && $user->awardBadge('roman_expert')) {
                $newBadgeUnlocked = 'roman_expert';
            }
        }

        // Sosyal Paylaşım URL'i
        $shareCardUrl = route('quiz.share-card', [
            'q' => $question->id,
            'correct' => $isCorrect ? 1 : 0,
            'streak' => $currentStreak,
            'points' => $user ? $user->numismatic_points : 50,
        ]);

        return [
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_option_index' => $question->correct_option_index,
            'explanation' => $question->explanation,
            'points_earned' => $pointsEarned,
            'total_points' => $user ? $user->numismatic_points : $pointsEarned,
            'current_streak_days' => $currentStreak,
            'new_badge' => $newBadgeUnlocked,
            'share_card_url' => $shareCardUrl,
            'share_text' => $isCorrect
                ? "Bugünkü antik nümizmatik sikke meydan okumasını %100 doğrulukla bildim! 🔥 {$currentStreak} Günlük Seri"
                : "Bugünkü antik sikke testini tamamladım. Nümizmatik bilgini test etmek için sen de katıl!",
        ];
    }

    /**
     * Sosyal Medya Paylaşım Kartı Görseli Oluşturur (1200x630 Piksel - GD / Intervention Uyumlu).
     */
    public function generateShareImage(bool $isCorrect, int $streak = 1, int $points = 50, ?string $rulerName = 'Roma İmparatorluk'): Response
    {
        $width = 1200;
        $height = 630;

        $im = imagecreatetruecolor($width, $height);

        // Koyu Nümizmatik Lüks Gradyan Zemin (Slate-950 to Slate-900)
        $darkBg = imagecolorallocate($im, 11, 17, 30);
        $cardBg = imagecolorallocate($im, 19, 29, 49);
        imagefilledrectangle($im, 0, 0, $width, $height, $darkBg);
        imagefilledrectangle($im, 40, 40, $width - 40, $height - 40, $cardBg);

        // Altın Kenarlık
        $goldBorder = imagecolorallocate($im, 245, 158, 11);
        imagerectangle($im, 40, 40, $width - 40, $height - 40, $goldBorder);
        imagerectangle($im, 44, 44, $width - 44, $height - 44, $goldBorder);

        // Renkler
        $white = imagecolorallocate($im, 255, 255, 255);
        $amber = imagecolorallocate($im, 251, 191, 36);
        $emerald = imagecolorallocate($im, 52, 211, 153);
        $slate = imagecolorallocate($im, 148, 163, 184);

        // Başlık
        $headerText = "TARIH NUMIZMATIK - GUNUN SIKKEST MEYDAN OKUMASI";
        imagestring($im, 5, 80, 80, $headerText, $amber);

        // Sonuç İfadesi
        if ($isCorrect) {
            $verdict = "BUGUNKU ROMA IMPARATORLUK SIKKESINI %100 DOGRULUKLA BILDIM!";
            imagestring($im, 5, 80, 160, $verdict, $emerald);
        } else {
            $verdict = "BUGUNKU ANTIK SIKKE TESTINI BASARIYLA TAMAMLADIM!";
            imagestring($im, 5, 80, 160, $verdict, $white);
        }

        // İstatistik Kutucukları
        $streakText = "SERI: " . $streak . " GUN KESINTISIZ";
        $pointsText = "TOPLAM PUAN: " . $points . " NUMIZMATIK PUANI";
        $statusText = "DERECE: UZMAN NUMIZMAT - AKADEMIK SEVIYE";

        imagestring($im, 5, 80, 240, $streakText, $amber);
        imagestring($im, 5, 80, 290, $pointsText, $white);
        imagestring($im, 4, 80, 340, $statusText, $slate);

        // Alt Platform Bilgisi
        $footerText = "Akademik Antik Sikke Katalogu ve Epigrafi Kutuphanesi: tarih.numis";
        imageline($im, 80, 520, $width - 80, 520, $goldBorder);
        imagestring($im, 4, 80, 545, $footerText, $slate);

        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Veritabanında bugün için soru yoksa katalogdan akıllı soru üretir.
     */
    protected function generateDailyQuestion(string $date): QuizQuestion
    {
        $coin = Coin::with(['ruler', 'period', 'primaryImage'])->inRandomOrder()->first()
            ?? Coin::first();

        $rulerName = $coin?->ruler?->name ?? 'Trajan';
        $wrongOptions = ['Hadrian', 'Augustus', 'Marcus Aurelius', 'Nero', 'Konstantin'];
        $wrongOptions = array_values(array_filter($wrongOptions, fn($r) => !str_contains($rulerName, $r)));
        shuffle($wrongOptions);

        $options = [
            $rulerName,
            $wrongOptions[0] ?? 'Hadrian',
            $wrongOptions[1] ?? 'Augustus',
            $wrongOptions[2] ?? 'Marcus Aurelius',
        ];
        shuffle($options);

        $correctIndex = array_search($rulerName, $options, true);

        return QuizQuestion::create([
            'coin_id' => $coin?->id,
            'question_type' => 'which_emperor',
            'question_text' => 'Görseldeki antik sikkenin ön yüzünde hangi imparator tasvir edilmiştir?',
            'options_json' => $options,
            'correct_option_index' => $correctIndex !== false ? $correctIndex : 0,
            'explanation' => "Doğru Cevap: {$rulerName}. Sikke ön yüz lejantı ({$coin?->obverse_legend}) ve portre ikonografisi doğrudan bu hükümdara aittir.",
            'is_daily_challenge' => true,
            'challenge_date' => $date,
        ]);
    }
}

