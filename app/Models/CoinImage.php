<?php

namespace App\Models;

use App\Enums\ImageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CoinImage extends Model
{
    use HasFactory;

    protected $table = 'coin_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'coin_id',
        'image_type',
        'image_path',
        'is_primary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'coin_id' => 'integer',
            'image_type' => ImageType::class,
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Model lifecycle event: Bir görsel is_primary yapıldığında diğer görselleri false yapar.
     */
    protected static function booted(): void
    {
        static::saving(function (CoinImage $image) {
            if ($image->is_primary) {
                static::where('coin_id', $image->coin_id)
                    ->where('id', '!=', $image->id ?? 0)
                    ->update(['is_primary' => false]);
            }
        });
    }

    /**
     * Görselin ait olduğu sikke.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    /**
     * Resmin tam web URL'ini döner (Storage link uyumlu).
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}

