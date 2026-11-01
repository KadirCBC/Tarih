<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_tier',
        'subscription_expires_at',
        'subscription_provider',
        'subscription_id',
        'is_expert',
        'expert_title',
        'numismatic_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'is_expert' => 'boolean',
            'numismatic_points' => 'integer',
        ];
    }

    /**
     * Kullanıcının Doğrulanmış Nümizmat / Editör olup olmadığını kontrol eder.
     */
    public function isExpert(): bool
    {
        return (bool) $this->is_expert;
    }

    /**
     * Kullanıcının kimlikleme talepleri.
     */
    public function identificationRequests(): HasMany
    {
        return $this->hasMany(IdentificationRequest::class);
    }

    /**
     * Kullanıcının yaptığı topluluk önerileri.
     */
    public function identificationComments(): HasMany
    {
        return $this->hasMany(IdentificationComment::class);
    }

    /**
     * Kullanıcının Nümizmat Pro üyesi olup olmadığını kontrol eder.
     */
    public function isPro(): bool
    {
        if ($this->subscription_tier !== 'pro') {
            return false;
        }

        // Süresiz pro veya süresi dolmamış aktif abonelik
        return is_null($this->subscription_expires_at) || $this->subscription_expires_at->isFuture();
    }

    /**
     * Günlük maksimum AI sikke tanıma hakkı (Pro: Sınırsız, Free: 3).
     */
    public function getDailyScanLimit(): int
    {
        return $this->isPro() ? 999999 : 3;
    }

    /**
     * Şahsi envantere eklenebilecek maksimum sikke adedi (Pro: Sınırsız, Free: 5).
     */
    public function getMaxCollectionLimit(): int
    {
        return $this->isPro() ? 999999 : 5;
    }

    /**
     * Kullanıcının envanter kotasını doldurup doldurmadığını denetler.
     */
    public function canAddToCollection(): bool
    {
        if ($this->isPro()) {
            return true;
        }

        return $this->collections()->count() < $this->getMaxCollectionLimit();
    }

    /**
     * Kullanıcının şahsi sanal sikke envanter kayıtları.
     */
    public function collections(): HasMany
    {
        return $this->hasMany(UserCollection::class);
    }

    /**
     * Kullanıcının envanterindeki sikkeler (Çoktan-çoğa ilişki).
     */
    public function coins(): BelongsToMany
    {
        return $this->belongsToMany(Coin::class, 'user_collections')
            ->withPivot('user_condition', 'purchase_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Kullanıcının kazandığı başarı rozetleri.
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Kullanıcının günlük seri ve puan kaydı.
     */
    public function streak(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserStreak::class)->withDefault([
            'current_streak_days' => 0,
            'longest_streak_days' => 0,
            'total_points' => 0,
        ]);
    }

    /**
     * Kullanıcıya nümizmatik puanı ekler.
     */
    public function addNumismaticPoints(int $points): void
    {
        $this->increment('numismatic_points', $points);
        $streak = $this->streak()->firstOrCreate(['user_id' => $this->id]);
        $streak->increment('total_points', $points);
    }

    /**
     * Kullanıcıya rozet kazandırır (varsa tekrar eklemez).
     */
    public function awardBadge(string $badgeType): bool
    {
        if ($this->achievements()->where('badge_type', $badgeType)->exists()) {
            return false;
        }

        $this->achievements()->create([
            'badge_type' => $badgeType,
            'unlocked_at' => now(),
        ]);

        return true;
    }
}
