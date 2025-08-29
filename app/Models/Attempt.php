<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'guest_name',
        'quiz_id',
        'score',
        'total',
        'started_at',
        'finished_at',
        'locale',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function getPlayerName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Unknown';
    }

    public function getPercentageScore(): float
    {
        return $this->total > 0 ? ($this->score / $this->total) * 100 : 0;
    }

    public function isCompleted(): bool
    {
        return $this->finished_at !== null;
    }
}
