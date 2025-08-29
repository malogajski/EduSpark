<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            ['key' => 'math', 'name' => ['sr' => 'Математика', 'en' => 'Mathematics', 'hu' => 'Matematika']],
            ['key' => 'serbian', 'name' => ['sr' => 'Српски језик', 'en' => 'Serbian Language', 'hu' => 'Szerb nyelv']],
            ['key' => 'science', 'name' => ['sr' => 'Природа и друштво', 'en' => 'Science', 'hu' => 'Természettudomány']],
            ['key' => 'english', 'name' => ['sr' => 'Енглески језик', 'en' => 'English Language', 'hu' => 'Angol nyelv']],
            ['key' => 'history', 'name' => ['sr' => 'Историја', 'en' => 'History', 'hu' => 'Történelem']],
        ];

        $subject = $this->faker->randomElement($subjects);

        return [
            'key' => $subject['key'] . '_' . $this->faker->unique()->numberBetween(1, 1000),
            'name' => $subject['name'],
        ];
    }
}