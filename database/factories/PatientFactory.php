<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'barangay_id' => \App\Models\Barangay::inRandomOrder()->value('id') ?? \App\Models\Barangay::factory(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'emergency_contact' => [
                'name' => $this->faker->name(),
                'relationship' => $this->faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend']),
                'phone' => $this->faker->numerify('09#########'),
            ],
            'allergies' => [$this->faker->optional()->sentence()],
            'current_medications' => [$this->faker->optional()->sentence()],
            'medical_history' => [$this->faker->optional()->paragraph()],
        ];
    }
}
