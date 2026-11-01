<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinVariant extends Model
{
    use HasFactory;

    protected $table = 'coin_variants';

    protected $fillable = [
        'coin_id',
        'variant_code',
        'obverse_diff',
        'reverse_diff',
        'rarity_shift',
    ];

    protected function casts(): array
    {
        return [
            'coin_id' => 'integer',
        ];
    }

    /**
     * Varyantın bağlı olduğu ana sikke kaydı.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}

