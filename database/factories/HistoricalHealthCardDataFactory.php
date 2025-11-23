<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricalHealthCardData>
 */
class HistoricalHealthCardDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'record_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'issued_count' => fake()->numberBetween(5, 50),
            'notes' => fake()->optional()->sentence(),
            'data_source' => fake()->randomElement(['manual', 'system']),
            'created_by' => null,
        ];
    }
}
