<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\User;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'guest_name' => null,
            'quiz_id' => Quiz::factory(),
            'score' => $this->faker->numberBetween(0, 10),
            'total' => 10,
            'started_at' => now(),
            'finished_at' => null,
            'locale' => 'sr',
        ];
    }

    /**
     * Indicate that the attempt is for a guest user.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => $this->faker->name(),
        ]);
    }

    /**
     * Indicate that the attempt is finished.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'finished_at' => now(),
        ]);
    }
}