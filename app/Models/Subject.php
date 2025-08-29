<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'name',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function getName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->name[$locale] ?? $this->name['sr'] ?? array_values($this->name)[0] ?? '';
    }

    public function setNameAttribute(array $value): void
    {
        if (isset($value['sr']) && !isset($value['en'])) {
            $value['en'] = $value['sr'];
        }
        if (isset($value['sr']) && !isset($value['hu'])) {
            $value['hu'] = $value['sr'];
        }
        $this->attributes['name'] = json_encode($value);
    }
}