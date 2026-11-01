<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruler extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'rulers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'period_id',
        'name',
        'title',
        'slug',
        'reign_start',
        'reign_end',
        'biography',
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
            'period_id' => 'integer',
            'reign_start' => 'integer',
            'reign_end' => 'integer',
        ];
    }

    /**
     * Hükümdarın ait olduğu dönem.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Hükümdar adına darbedilen sikkeler.
     */
    public function coins(): HasMany
    {
        return $this->hasMany(Coin::class);
    }

    /**
     * Hüküm süresi aralığını okunabilir formatta verir.
     */
    public function getFormattedReignAttribute(): string
    {
        $formatYear = function (?int $year) {
            if ($year === null) {
                return null;
            }
            return $year < 0 ? 'M.Ö. ' . abs($year) : 'M.S. ' . $year;
        };

        $start = $formatYear($this->reign_start);
        $end = $formatYear($this->reign_end);

        if ($start && $end) {
            return "{$start} - {$end}";
        }

        return $start ?? $end ?? 'Bilinmiyor';
    }
}

