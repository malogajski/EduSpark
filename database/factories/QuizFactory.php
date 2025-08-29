<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade' => $this->faker->numberBetween(1, 8),
            'subject_id' => Subject::factory(),
            'title' => [
                'sr' => $this->faker->sentence(3),
                'en' => $this->faker->sentence(3),
                'hu' => $this->faker->sentence(3),
            ],
            'description' => [
                'sr' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
                'hu' => $this->faker->paragraph(),
            ],
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the quiz is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}