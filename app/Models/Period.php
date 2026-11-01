<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'start_year',
        'end_year',
        'description',
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
            'start_year' => 'integer',
            'end_year' => 'integer',
        ];
    }

    /**
     * Bu döneme ait hükümdarlar/imparatorlar.
     */
    public function rulers(): HasMany
    {
        return $this->hasMany(Ruler::class);
    }

    /**
     * Bu döneme ait sikkeler.
     */
    public function coins(): HasMany
    {
        return $this->hasMany(Coin::class);
    }

    /**
     * Dönem tarih aralığını okunabilir formatta verir (örn: M.Ö. 27 - M.S. 476).
     */
    public function getFormattedDateRangeAttribute(): string
    {
        $formatYear = function (?int $year) {
            if ($year === null) {
                return null;
            }
            return $year < 0 ? 'M.Ö. ' . abs($year) : 'M.S. ' . $year;
        };

        $start = $formatYear($this->start_year);
        $end = $formatYear($this->end_year);

        if ($start && $end) {
            return "{$start} - {$end}";
        }

        return $start ?? $end ?? 'Tarih Belirtilmemiş';
    }
}

