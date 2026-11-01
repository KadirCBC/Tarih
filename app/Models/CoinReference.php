<?php

namespace App\Models;

use App\Enums\CatalogSystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinReference extends Model
{
    use HasFactory;

    protected $table = 'coin_references';

    protected $fillable = [
        'coin_id',
        'catalog_system',
        'reference_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'coin_id' => 'integer',
        ];
    }

    /**
     * Referansın ait olduğu ana sikke kaydı.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    /**
     * Katalog sistemi ve referans numarasına göre arama scope'u.
     */
    public function scopeLookup(Builder $query, string $system, string $number): Builder
    {
        return $query->where('catalog_system', 'LIKE', $system)
            ->where(function ($q) use ($number) {
                $q->where('reference_number', $number)
                  ->orWhere('reference_number', 'LIKE', "%{$number}%");
            });
    }

    /**
     * Biçimlendirilmiş tam referans etiketi (Örn: "RIC II 142").
     */
    public function getFullReferenceAttribute(): string
    {
        return "{$this->catalog_system} {$this->reference_number}";
    }
}

