<?php

namespace App\Models;

use App\Enums\CoinMetal;
use App\Enums\RarityGrade;
use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coin extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'coins';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'period_id',
        'ruler_id',
        'mint_id',
        'title',
        'slug',
        'catalog_reference',
        'denomination',
        'metal',
        'weight_g',
        'diameter_mm',
        'obverse_desc',
        'obverse_legend',
        'obverse_legend_trans',
        'reverse_desc',
        'reverse_legend',
        'reverse_legend_trans',
        'rarity_grade',
        'historical_notes',
        'view_count',
    ];

    /**
     * Source column used by HasUniqueSlug trait.
     */
    protected string $slugSource = 'title';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'period_id' => 'integer',
            'ruler_id' => 'integer',
            'mint_id' => 'integer',
            'metal' => CoinMetal::class,
            'rarity_grade' => RarityGrade::class,
            'weight_g' => 'decimal:2',
            'diameter_mm' => 'decimal:1',
            'view_count' => 'integer',
        ];
    }

    /**
     * Sikkenin ait olduğu tarihsel dönem.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Sikkenin basıldığı dönemin hükümdarı/imparatoru.
     */
    public function ruler(): BelongsTo
    {
        return $this->belongsTo(Ruler::class);
    }

    /**
     * Sikkenin darbedildiği darphane / antik kent.
     */
    public function mint(): BelongsTo
    {
        return $this->belongsTo(Mint::class);
    }

    /**
     * Uluslararası müzayede ve kondisyona göre değerleme kayıtları.
     */
    public function valuations(): HasMany
    {
        return $this->hasMany(CoinValuation::class);
    }

    /**
     * Sikkeye ait tüm fotoğraflar / avers-revers çizimleri.
     */
    public function images(): HasMany
    {
        return $this->hasMany(CoinImage::class);
    }

    /**
     * Sikkenin ana / kapak fotoğrafı.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(CoinImage::class)->where('is_primary', true);
    }

    /**
     * Kullanıcıların sanal envanter kayıtları.
     */
    public function userCollections(): HasMany
    {
        return $this->hasMany(UserCollection::class);
    }

    /**
     * Bu sikkeyi envanterine eklemiş koleksiyonerler (User modeli ile çoktan-çoğa).
     */
    public function collectors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_collections')
            ->withPivot('user_condition', 'purchase_date', 'notes')
            ->withTimestamps();
    }

    /**
     * MySQL Fulltext ve kısaltma/akademik lejant uyumlu arama motoru.
     * "AVG", "IMP", "TRAIAN" gibi 3 harfli lejant ve katalog kısaltmalarını da eksiksiz yakalar.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if (empty($term)) {
            return $query;
        }

        // Temizlenmiş kelimelerden Boolean fulltext araması (+AVG* +CAES*)
        $words = array_filter(preg_split('/\s+/', $term) ?: []);
        $booleanParts = array_map(function ($word) {
            $clean = preg_replace('/[^\p{L}\p{N}_]+/u', '', $word);
            return !empty($clean) ? "+{$clean}*" : '';
        }, $words);
        $booleanTerm = trim(implode(' ', array_filter($booleanParts)));

        return $query->where(function (Builder $subQuery) use ($booleanTerm, $term) {
            if (!empty($booleanTerm)) {
                $subQuery->whereRaw(
                    "MATCH(title, obverse_legend, reverse_legend, catalog_reference) AGAINST(? IN BOOLEAN MODE)",
                    [$booleanTerm]
                );
            }

            // MySQL minimum token sınırı (ft_min_word_len) ve özel karakterler için fallback
            $subQuery->orWhere('title', 'like', "%{$term}%")
                ->orWhere('obverse_legend', 'like', "%{$term}%")
                ->orWhere('reverse_legend', 'like', "%{$term}%")
                ->orWhere('catalog_reference', 'like', "%{$term}%")
                ->orWhere('denomination', 'like', "%{$term}%");
        });
    }

    /**
     * Maden türüne göre filtreleme.
     */
    public function scopeByMetal(Builder $query, CoinMetal|string $metal): Builder
    {
        $value = $metal instanceof CoinMetal ? $metal->value : $metal;
        return $query->where('metal', $value);
    }

    /**
     * Nadirlik derecesine göre filtreleme.
     */
    public function scopeByRarity(Builder $query, RarityGrade|string $rarity): Builder
    {
        $value = $rarity instanceof RarityGrade ? $rarity->value : $rarity;
        return $query->where('rarity_grade', $value);
    }

    /**
     * Görüntülenme sayısını artırır.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}

