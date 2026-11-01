<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MintErrorType extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $table = 'mint_error_types';

    protected $fillable = [
        'name',
        'slug',
        'severity_impact',
        'multiplier_min',
        'multiplier_max',
        'visual_guide_image',
        'explanation_tr',
    ];

    protected $casts = [
        'multiplier_min' => 'decimal:2',
        'multiplier_max' => 'decimal:2',
    ];

    protected string $slugSource = 'name';

    /**
     * Bu darp hatasına sahip kullanıcı envanter kayıtları.
     */
    public function userCollections(): HasMany
    {
        return $this->hasMany(UserCollection::class, 'mint_error_type_id');
    }
}

