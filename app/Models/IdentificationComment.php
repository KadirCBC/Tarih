<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentificationComment extends Model
{
    use HasFactory;

    protected $table = 'identification_comments';

    protected $fillable = [
        'identification_request_id',
        'user_id',
        'suggested_coin_id',
        'comment',
        'is_expert_verification',
    ];

    protected function casts(): array
    {
        return [
            'identification_request_id' => 'integer',
            'user_id' => 'integer',
            'suggested_coin_id' => 'integer',
            'is_expert_verification' => 'boolean',
        ];
    }

    /**
     * Yorumun ait olduğu kimlikleme talebi.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(IdentificationRequest::class, 'identification_request_id');
    }

    /**
     * Yorumu yapan kullanıcı.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kullanıcının önerdiği katalog sikkesi.
     */
    public function suggestedCoin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'suggested_coin_id');
    }
}

