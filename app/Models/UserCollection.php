<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'user_condition' => ConditionGrade::class,
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
}

