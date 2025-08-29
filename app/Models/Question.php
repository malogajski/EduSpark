<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_id',
        'prompt',
        'explanation',
        'question_type',
        'order',
    ];

    protected $casts = [
        'prompt' => 'array',
        'explanation' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function getPrompt(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->prompt[$locale] ?? $this->prompt['sr'] ?? array_values($this->prompt)[0] ?? '';
    }

    public function getExplanation(?string $locale = null): ?string
    {
        if (!$this->explanation) {
            return null;
        }
        $locale = $locale ?? app()->getLocale();
        return $this->explanation[$locale] ?? $this->explanation['sr'] ?? array_values($this->explanation)[0] ?? null;
    }

    public function setPromptAttribute(array $value): void
    {
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['prompt'] = json_encode($value);
    }

    public function setExplanationAttribute(?array $value): void
    {
        if ($value === null) {
            $this->attributes['explanation'] = null;
            return;
        }
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['explanation'] = json_encode($value);
    }

    public function isSingleChoice(): bool
    {
        return $this->question_type === 'single_choice';
    }

    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isTrueFalse(): bool
    {
        return $this->question_type === 'true_false';
    }

    public function getQuestionTypeLabel(): string
    {
        return match ($this->question_type) {
            'single_choice' => 'Single Choice',
            'multiple_choice' => 'Multiple Choice', 
            'true_false' => 'True/False',
            default => 'Unknown'
        };
    }
}