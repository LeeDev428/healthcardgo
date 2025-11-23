<?php

namespace App\Livewire\Doctor;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PatientsList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $doctorId = Auth::user()?->doctor?->id;

        $query = Patient::query()
            ->with('user')
            ->whereHas('appointments', function ($q) use ($doctorId) {
                $q->when($doctorId, fn ($qq) => $qq->where('doctor_id', $doctorId));
            });

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('patient_number', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }

        $patients = $query->orderByDesc('created_at')->paginate(12);

        return view('livewire.doctor.patients-list', compact('patients'));
    }
}
