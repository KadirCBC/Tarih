<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    protected $fillable = [
        'coin_id',
        'question_type',
        'question_text',
        'options_json',
        'correct_option_index',
        'explanation',
        'is_daily_challenge',
        'challenge_date',
    ];

    protected $casts = [
        'coin_id' => 'integer',
        'options_json' => 'array',
        'correct_option_index' => 'integer',
        'is_daily_challenge' => 'boolean',
        'challenge_date' => 'date',
    ];

    /**
     * Sorunun ilişkili olduğu sikke.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}

