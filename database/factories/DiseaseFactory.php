<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disease>
 */
class DiseaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $onsetDate = $this->faker->dateTimeBetween('-30 days', '-7 days');
        $reportedDate = $this->faker->dateTimeBetween($onsetDate, '-5 days');
        $diagnosisDate = $this->faker->dateTimeBetween($reportedDate, 'now');
        $confirmedDate = $this->faker->boolean(80) ? $this->faker->dateTimeBetween($diagnosisDate, 'now') : null;

        return [
            'patient_id' => \App\Models\Patient::factory(),
            'medical_record_id' => null,
            'disease_type' => $this->faker->randomElement(['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications']),
            'case_number' => 'CASE-'.now()->format('Y').'-'.$this->faker->unique()->numerify('######'),
            'status' => $this->faker->randomElement(['suspected', 'confirmed', 'ruled_out']),
            'onset_date' => $onsetDate,
            'reported_date' => $reportedDate,
            'confirmed_date' => $confirmedDate,
            'diagnosis_date' => $diagnosisDate,
            'barangay_id' => \App\Models\Barangay::factory(),
            'symptoms' => $this->faker->randomElements(['fever', 'cough', 'headache', 'body_aches', 'rash', 'fatigue'], $this->faker->numberBetween(2, 5)),
            'risk_factors' => $this->faker->sentence(),
            'treatment_notes' => $this->faker->paragraph(),
            'severity' => $this->faker->randomElement(['mild', 'moderate', 'severe']),
            'reported_by' => \App\Models\User::factory(),
        ];
    }

    /**
     * Indicate that the disease is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_date' => now(),
        ]);
    }

    /**
     * Indicate that the disease is suspected.
     */
    public function suspected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspected',
            'confirmed_date' => null,
        ]);
    }

    /**
     * Set specific disease type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'disease_type' => $type,
        ]);
    }

    /**
     * Set specific severity.
     */
    public function severity(string $severity): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => $severity,
        ]);
    }
}
