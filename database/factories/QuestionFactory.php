<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'prompt' => [
                'sr' => $this->faker->sentence() . '?',
                'en' => $this->faker->sentence() . '?',
                'hu' => $this->faker->sentence() . '?',
            ],
            'explanation' => [
                'sr' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
                'hu' => $this->faker->paragraph(),
            ],
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}