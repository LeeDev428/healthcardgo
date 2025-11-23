<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $scheduled = now()->addDays($this->faker->numberBetween(0, 10))->setTime(
            $this->faker->numberBetween(8, 16),
            $this->faker->randomElement([0, 15, 30, 45])
        );

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => null, // Assign later or via state
            'service_id' => Service::factory(),
            'scheduled_at' => $scheduled,
            'queue_number' => $this->faker->numberBetween(1, 50),
            'status' => 'pending',
            'notes' => null,
            'cancellation_reason' => null,
            'reminder_sent' => [],
            'fee' => $this->faker->randomFloat(2, 50, 500),
        ];
    }

    public function confirmed(): self
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function checkedIn(): self
    {
        return $this->state(fn () => ['status' => 'checked_in', 'check_in_at' => now()]);
    }

    public function inProgress(): self
    {
        return $this->state(fn () => ['status' => 'in_progress', 'started_at' => now()]);
    }

    public function completed(): self
    {
        return $this->state(fn () => ['status' => 'completed', 'completed_at' => now()]);
    }

    public function cancelled(): self
    {
        return $this->state(fn () => ['status' => 'cancelled', 'cancellation_reason' => $this->faker->sentence()]);
    }

    public function noShow(): self
    {
        return $this->state(fn () => ['status' => 'no_show']);
    }
}
