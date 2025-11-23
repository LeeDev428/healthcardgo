<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('Appointment Details')]
class AppointmentDetails extends Component
{
    public ?Appointment $appointment = null;

    public bool $showCancelModal = false;

    public string $cancellationReason = '';

    public function mount(Appointment $appointment): void
    {
        $patientId = Auth::user()?->patient?->id;

        abort_unless($patientId && $appointment->patient_id === $patientId, 403);

        $this->appointment = $appointment->load(['service', 'patient.user', 'doctor']);
    }

    public function refresh(): void
    {
        if ($this->appointment) {
            $this->appointment->refresh()->load(['service', 'patient.user', 'doctor']);
        }
    }

    public function openCancelModal(): void
    {
        $this->authorizeOwnership();
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
    }

    public function cancelAppointment(): mixed
    {
        $this->authorizeOwnership();

        if (! $this->appointment->canBeCancelled()) {
            Session::flash('error', 'Appointment cannot be cancelled (must be 24+ hours before scheduled time).');

            return null;
        }

        try {
            $service = app(AppointmentService::class);
            $service->cancelAppointment($this->appointment, $this->cancellationReason, false);

            Session::flash('success', 'Appointment cancelled successfully.');

            return Redirect::route('patient.appointments.list');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());

            return null;
        }
    }

    protected function authorizeOwnership(): void
    {
        $patientId = Auth::user()?->patient?->id;
        abort_unless($patientId && $this->appointment && $this->appointment->patient_id === $patientId, 403);
    }

    public function render()
    {
        return view('livewire.patient.appointment-details', [
            'appointment' => $this->appointment,
        ]);
    }
}
