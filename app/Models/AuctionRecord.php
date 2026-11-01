<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionRecord extends Model
{
    use HasFactory;

    protected $table = 'auction_records';

    protected $fillable = [
        'coin_id',
        'auction_house',
        'sale_name_or_number',
        'lot_number',
        'sale_date',
        'hammer_price_usd',
        'realized_price_usd',
        'condition_grade',
        'source_url',
    ];

    protected function casts(): array
    {
        return [
            'coin_id' => 'integer',
            'sale_date' => 'date',
            'hammer_price_usd' => 'float',
            'realized_price_usd' => 'float',
            'condition_grade' => ConditionGrade::class,
        ];
    }

    /**
     * Müzayede kaydının ait olduğu sikke.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    /**
     * Belirtilen ay içindeki satış kayıtlarını getiren scope.
     */
    public function scopeRecentMonths(Builder $query, int $months = 24): Builder
    {
        return $query->where('sale_date', '>=', now()->subMonths($months));
    }

    /**
     * Biçimlendirilmiş gerçekleşen satış fiyatı (Örn: "$420").
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->realized_price_usd, 0, ',', '.');
    }
}

