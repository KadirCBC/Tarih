<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class IdentificationRequest extends Model
{
    use HasFactory;

    protected $table = 'identification_requests';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'obverse_image',
        'reverse_image',
        'weight_g',
        'diameter_mm',
        'status',
        'identified_coin_id',
        'verified_by_user_id',
        'expert_notes',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'weight_g' => 'float',
            'diameter_mm' => 'float',
            'identified_coin_id' => 'integer',
            'verified_by_user_id' => 'integer',
        ];
    }

    /**
     * Talebi açan kullanıcı.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Eşleştirilen katalog sikkesi.
     */
    public function identifiedCoin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'identified_coin_id');
    }

    /**
     * Onaylayan doğrulanmış nümizmat / editör.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Topluluk önerileri ve yorumları.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(IdentificationComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Ön yüz görsel URL'si.
     */
    public function getObverseUrlAttribute(): string
    {
        if (str_starts_with($this->obverse_image, 'http')) {
            return $this->obverse_image;
        }

        return Storage::disk('public')->url($this->obverse_image);
    }

    /**
     * Arka yüz görsel URL'si.
     */
    public function getReverseUrlAttribute(): ?string
    {
        if (!$this->reverse_image) {
            return null;
        }

        if (str_starts_with($this->reverse_image, 'http')) {
            return $this->reverse_image;
        }

        return Storage::disk('public')->url($this->reverse_image);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isIdentified(): bool
    {
        return $this->status === 'identified';
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeIdentified(Builder $query): Builder
    {
        return $query->where('status', 'identified');
    }

    /**
     * Durum etiketi ve renk sınıfları.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'identified' => [
                'label' => 'Uzman Onaylı (Teşhis Edildi)',
                'color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                'icon' => '✓',
            ],
            'disputed' => [
                'label' => 'Tartışmalı / İncelemede',
                'color' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                'icon' => '?',
            ],
            'rejected' => [
                'label' => 'Reddedildi / Replikasyon',
                'color' => 'bg-red-500/20 text-red-300 border-red-500/30',
                'icon' => '✕',
            ],
            default => [
                'label' => 'Uzman İncelemesi Bekliyor',
                'color' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                'icon' => '⏳',
            ],
        };
    }
}

