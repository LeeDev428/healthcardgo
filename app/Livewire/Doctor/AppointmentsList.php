<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentsList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $doctorId = Auth::user()?->doctor?->id;

        $query = Appointment::query()
            ->with(['patient.user', 'service'])
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId));

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('appointment_number', 'like', "%{$s}%")
                    ->orWhereHas('patient.user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('service', fn ($sq) => $sq->where('name', 'like', "%{$s}%"));
            });
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        $appointments = $query->orderByDesc('scheduled_at')->paginate(10);

        return view('livewire.doctor.appointments-list', compact('appointments'));
    }
}
