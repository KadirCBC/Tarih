<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use App\Observers\UserCollectionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([UserCollectionObserver::class])]
class UserCollection extends Model
{
    use HasFactory;

    protected $table = 'user_collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'coin_id',
        'user_condition',
        'has_mint_error',
        'mint_error_type_id',
        'mint_error_notes',
        'custom_image_path',
        'purchase_price_usd',
        'purchase_date',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'coin_id' => 'integer',
            'has_mint_error' => 'boolean',
            'mint_error_type_id' => 'integer',
            'user_condition' => ConditionGrade::class,
            'purchase_price_usd' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    /**
     * Envanter kaydının sahibi olan kullanıcı.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Envantere eklenen sikke.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    /**
     * Sikkenin sahip olduğu darp hatası türü (Varsa).
     */
    public function mintErrorType(): BelongsTo
    {
        return $this->belongsTo(MintErrorType::class, 'mint_error_type_id');
    }

    /**
     * Kullanıcının yüklediği özel sikke fotoğrafının URL'i.
     */
    public function getCustomImageUrlAttribute(): ?string
    {
        if (empty($this->custom_image_path)) {
            return null;
        }

        if (str_starts_with($this->custom_image_path, 'http')) {
            return $this->custom_image_path;
        }

        return Storage::disk('public')->url($this->custom_image_path);
    }

    /**
     * Dinamik filigran uygulanmış güvenli görsel URL'i (Orijinal bozulmadan yasal filigran basılır).
     */
    public function getWatermarkedImageUrlAttribute(): ?string
    {
        if (empty($this->custom_image_path)) {
            return null;
        }

        return route('image.watermark', ['path' => $this->custom_image_path]);
    }

    /**
     * Görüntülenecek görsel (Özel fotoğraf varsa o, yoksa katalog ana görseli).
     */
    public function getDisplayImageUrlAttribute(): ?string
    {
        return $this->custom_image_url ?? $this->coin?->primaryImage?->url;
    }

    /**
     * Kullanıcının belirlediği kondisyona karşılık gelen katalog değerlemesini bulur.
     */
    public function getEstimatedValuationAttribute(): ?CoinValuation
    {
        if (!$this->relationLoaded('coin') || !$this->coin->relationLoaded('valuations')) {
            return $this->coin?->valuations()
                ->where('condition_grade', $this->user_condition->value)
                ->first();
        }

        return $this->coin->valuations->first(function ($val) {
            $grade = $val->condition_grade instanceof ConditionGrade
                ? $val->condition_grade->value
                : $val->condition_grade;
            return $grade === $this->user_condition->value;
        });
    }
}
