<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.patient')]
#[Title('Appointments List')]
class AppointmentsList extends Component
{
    use WithPagination;

    public string $tab = 'upcoming'; // upcoming|past|cancelled

    public ?int $patientId = null;

    public bool $showCancelModal = false;

    public ?int $selectedAppointmentId = null;

    public string $cancellationReason = '';

    public function mount(): void
    {
        $this->patientId = Auth::user()?->patient?->id;
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['upcoming', 'past', 'cancelled']) ? $tab : 'upcoming';
        $this->resetPage();
    }

    public function refreshList(): void
    {
        // Intentionally empty; used by wire:poll to re-render
    }

    public function openCancelModal(int $appointmentId): void
    {
        $this->selectedAppointmentId = $appointmentId;
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->selectedAppointmentId = null;
        $this->cancellationReason = '';
    }

    public function cancelAppointment(): void
    {
        if (! $this->selectedAppointmentId || ! $this->patientId) {
            return;
        }

        $appointment = Appointment::where('id', $this->selectedAppointmentId)
            ->where('patient_id', $this->patientId)
            ->first();

        if (! $appointment) {
            Session::flash('error', 'Appointment not found.');

            return;
        }

        if (! $appointment->canBeCancelled()) {
            Session::flash('error', 'Appointment cannot be cancelled (must be 24+ hours before scheduled time).');

            return;
        }

        try {
            app(AppointmentService::class)->cancelAppointment($appointment, $this->cancellationReason, false);
            Session::flash('success', 'Appointment cancelled successfully.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        } finally {
            $this->closeCancelModal();
            $this->resetPage();
        }
    }

    public function render()
    {
        $appointments = collect();

        if ($this->patientId) {
            $query = Appointment::query()
                ->where('patient_id', $this->patientId)
                ->with(['service'])
                ->orderByDesc('scheduled_at');

            if ($this->tab === 'upcoming') {
                $query->whereDate('scheduled_at', '>=', now())
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress']);
            } elseif ($this->tab === 'past') {
                $query->whereDate('scheduled_at', '<', now())
                    ->whereIn('status', ['completed', 'no_show']);
            } else { // cancelled
                $query->where('status', 'cancelled');
            }

            $appointments = $query->paginate(10);
        }

        return view('livewire.patient.appointments-list', [
            'appointments' => $appointments,
        ]);
    }
}
