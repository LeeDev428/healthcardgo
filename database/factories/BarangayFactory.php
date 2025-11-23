<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barangay>
 */
class BarangayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'city' => $this->faker->city(),
            'latitude' => $this->faker->randomFloat(6, 14.0, 15.0),
            'longitude' => $this->faker->randomFloat(6, 120.0, 122.0),
            'population' => $this->faker->numberBetween(1000, 50000),
            'boundaries' => null,
        ];
    }
}
