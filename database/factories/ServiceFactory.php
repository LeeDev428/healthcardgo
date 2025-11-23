<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'General Consultation',
                'Prenatal Care',
                'Immunization',
                'HIV Testing',
                'Healthcard Processing',
                'Dental Services',
                'Laboratory Services',
            ]),
            'description' => $this->faker->sentence(),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60]),
            'is_active' => true,
        ];
    }
}
