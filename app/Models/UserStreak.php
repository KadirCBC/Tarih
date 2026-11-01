<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStreak extends Model
{
    use HasFactory;

    protected $table = 'user_streaks';

    protected $fillable = [
        'user_id',
        'current_streak_days',
        'longest_streak_days',
        'total_points',
        'last_activity_date',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'current_streak_days' => 'integer',
        'longest_streak_days' => 'integer',
        'total_points' => 'integer',
        'last_activity_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

