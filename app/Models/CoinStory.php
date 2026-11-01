<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CoinStory extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'coin_stories';

    protected $fillable = [
        'coin_id',
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'is_coin_of_the_week',
        'published_at',
        'view_count',
    ];

    protected string $slugSource = 'title';

    protected function casts(): array
    {
        return [
            'is_coin_of_the_week' => 'boolean',
            'published_at' => 'datetime',
            'view_count' => 'integer',
        ];
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    public function scopeCoinOfTheWeek(Builder $query): Builder
    {
        return $query->where('is_coin_of_the_week', true)->published();
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!empty($this->cover_image)) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            return Storage::disk('public')->url($this->cover_image);
        }

        return $this->coin?->primaryImage?->url ?? url('/images/default-discover-cover.jpg');
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}

