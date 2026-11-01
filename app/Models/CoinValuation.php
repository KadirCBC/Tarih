<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinValuation extends Model
{
    use HasFactory;

    protected $table = 'coin_valuations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'coin_id',
        'condition_grade',
        'min_value_usd',
        'max_value_usd',
        'last_updated_at',
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
            'condition_grade' => ConditionGrade::class,
            'min_value_usd' => 'decimal:2',
            'max_value_usd' => 'decimal:2',
            'last_updated_at' => 'date',
        ];
    }

    /**
     * Değerlemenin ait olduğu sikke.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    /**
     * Değer aralığını biçimlendirilmiş USD olarak verir (örn: $250.00 - $400.00).
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        $min = number_format((float) $this->min_value_usd, 2);
        $max = number_format((float) $this->max_value_usd, 2);

        return "\${$min} - \${$max} USD";
    }
}

