<?php

namespace App\Models;

use App\Enums\CoinMetal;
use App\Enums\RarityGrade;
use App\Traits\HasMultilingualFields;
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
    use HasFactory, HasUniqueSlug, HasMultilingualFields;

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
        'title_translations',
        'slug',
        'catalog_reference',
        'denomination',
        'metal',
        'weight_g',
        'diameter_mm',
        'obverse_desc',
        'obverse_desc_translations',
        'obverse_legend',
        'obverse_legend_trans',
        'obverse_legend_trans_translations',
        'reverse_desc',
        'reverse_desc_translations',
        'reverse_legend',
        'reverse_legend_trans',
        'reverse_legend_trans_translations',
        'rarity_grade',
        'historical_notes',
        'historical_notes_translations',
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
            'title_translations' => 'array',
            'obverse_desc_translations' => 'array',
            'obverse_legend_trans_translations' => 'array',
            'reverse_desc_translations' => 'array',
            'reverse_legend_trans_translations' => 'array',
            'historical_notes_translations' => 'array',
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
     * Sikkeye ait çapraz katalog referansları (Concordance: RIC, BMC, Sear, Cohen, Crawford, RSC vb.).
     */
    public function references(): HasMany
    {
        return $this->hasMany(CoinReference::class);
    }

    /**
     * Sikkeye ait tipolojik varyasyonlar (lejant kırılması, darphane monogramı farkları).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(CoinVariant::class);
    }

    /**
     * Uluslararası açık artırma evlerinin gerçekleşen satış kayıtları.
     */
    public function auctionRecords(): HasMany
    {
        return $this->hasMany(AuctionRecord::class);
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
                ->orWhere('denomination', 'like', "%{$term}%")
                ->orWhereHas('references', function (Builder $refQuery) use ($term) {
                    $refQuery->where('reference_number', 'like', "%{$term}%")
                             ->orWhereRaw("CONCAT(catalog_system, ' ', reference_number) LIKE ?", ["%{$term}%"]);
                })
                ->orWhereHas('variants', function (Builder $varQuery) use ($term) {
                    $varQuery->where('variant_code', 'like', "%{$term}%");
                });
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

