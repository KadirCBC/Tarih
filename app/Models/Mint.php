<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mint extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'mints';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'location',
        'latitude',
        'longitude',
    ];

    /**
     * Source column used by HasUniqueSlug trait.
     */
    protected string $slugSource = 'name';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Bu darphanede basılmış sikkeler.
     */
    public function coins(): HasMany
    {
        return $this->hasMany(Coin::class);
    }

    /**
     * Koordinat varlığını kontrol eder (Harita gösterimi için).
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}

