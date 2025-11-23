<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthCard>
 */
class HealthCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'card_number' => 'HC'.date('Y').str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'issue_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
            'qr_code' => 'data:image/png;base64,'.base64_encode(fake()->text(100)),
            'status' => fake()->randomElement(['active', 'expired', 'suspended']),
            'medical_data' => [
                'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'allergies' => fake()->optional()->words(3, true),
                'emergency_contact' => fake()->phoneNumber(),
            ],
            'last_renewed_at' => null,
        ];
    }
}
