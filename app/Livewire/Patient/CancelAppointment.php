<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Models\Notification;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CancelAppointment extends Component
{
    public Appointment $appointment;

    public $showModal = false;

    public $cancellationReason = '';

    protected $rules = [
        'cancellationReason' => 'required|string|min:10|max:500',
    ];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;

        // Check if user owns this appointment
        if ($this->appointment->patient_id !== Auth::user()->patient->id) {
            abort(403, 'Unauthorized');
        }
    }

    public function openModal()
    {
        // Verify can still be cancelled
        if (! $this->appointment->canBeCancelled()) {
            session()->flash('error', 'This appointment cannot be cancelled. Must be at least 24 hours before the scheduled time.');

            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('cancellationReason');
        $this->resetValidation();
    }

    public function cancel()
    {
        $this->validate();

        try {
            $appointmentService = app(AppointmentService::class);
            $appointmentService->cancelAppointment($this->appointment, $this->cancellationReason, false);

            // Create notification for admins
            Notification::create([
                'user_id' => null, // System notification
                'type' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled',
                'message' => 'Patient '.$this->appointment->patient->user->name.' cancelled appointment #'.$this->appointment->appointment_number,
                'data' => [
                    'appointment_id' => $this->appointment->id,
                    'patient_id' => $this->appointment->patient_id,
                    'reason' => $this->cancellationReason,
                ],
            ]);

            session()->flash('success', 'Appointment cancelled successfully. Queue numbers have been recalculated.');

            return redirect()->route('patient.dashboard');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.patient.cancel-appointment');
    }
}
