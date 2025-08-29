<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'text' => 'array',
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function getText(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->text[$locale] ?? $this->text['sr'] ?? array_values($this->text)[0] ?? '';
    }

    public function setTextAttribute(array $value): void
    {
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['text'] = json_encode($value);
    }
}
