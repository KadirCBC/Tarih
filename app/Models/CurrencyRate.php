<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'rate_to_eur',
        'rate_to_usd',
        'is_active',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'rate_to_eur' => 'float',
            'rate_to_usd' => 'float',
            'is_active' => 'boolean',
            'fetched_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

