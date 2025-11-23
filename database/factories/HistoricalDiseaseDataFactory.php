<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricalDiseaseData>
 */
class HistoricalDiseaseDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'disease_type' => $this->faker->randomElement(['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications']),
            'barangay_id' => \App\Models\Barangay::factory(),
            'record_date' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'case_count' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->optional()->sentence(),
            // data_source must match enum in migration: ['manual', 'imported', 'system']
            'data_source' => $this->faker->randomElement(['manual', 'imported', 'system']),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
