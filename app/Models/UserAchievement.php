<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    use HasFactory;

    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id',
        'badge_type',
        'unlocked_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Rozetin insan dostu etiket ve görsel açıklaması.
     */
    public function getBadgeInfoAttribute(): array
    {
        return match ($this->badge_type) {
            'novice_collector' => [
                'name' => 'Çırak Nümizmat',
                'icon' => '🪙',
                'description' => 'İlk günlük sikke yarışmasını başarıyla tamamladınız.',
            ],
            'roman_expert' => [
                'name' => 'Roma Uzmanı',
                'icon' => '🏛️',
                'description' => 'Roma imparatorluk sikkelerinde 5 doğru tahmine ulaştınız.',
            ],
            'legend_reader' => [
                'name' => 'Epigrafi Ustası',
                'icon' => '📜',
                'description' => 'Antik lejant ve Latince yazıt çevirilerini hatasız çözdünüz.',
            ],
            'keen_eye' => [
                'name' => 'Keskin Göz',
                'icon' => '🦅',
                'description' => 'Darp hataları ve döküm sahtelik testlerini başarıyla geçtiniz.',
            ],
            default => [
                'name' => 'Nümizmat Rozeti',
                'icon' => '⭐',
                'description' => 'Platform başarısı kazanıldı.',
            ],
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

