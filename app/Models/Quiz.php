<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'grade',
        'subject_id',
        'title',
        'description',
        'is_published',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_published' => 'boolean',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function getTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->title[$locale] ?? $this->title['sr'] ?? array_values($this->title)[0] ?? '';
    }

    public function getDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->description[$locale] ?? $this->description['sr'] ?? array_values($this->description)[0] ?? '';
    }

    public function setTitleAttribute(array $value): void
    {
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['title'] = json_encode($value);
    }

    public function setDescriptionAttribute(array $value): void
    {
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['description'] = json_encode($value);
    }
}