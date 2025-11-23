<?php

namespace App\Livewire\HealthcareAdmin;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    public $search = '';

    public $genderFilter = '';

    public $barangayFilter = '';

    public $selectedPatient = null;

    public $showDetailsModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBarangayFilter(): void
    {
        $this->resetPage();
    }

    public function viewDetails($patientId): void
    {
        $this->selectedPatient = Patient::with(['user', 'barangay', 'appointments.service', 'healthCards'])
            ->find($patientId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedPatient = null;
    }

    public function getStatisticsProperty(): array
    {
        $user = Auth::user();
        $adminCategory = $user->admin_category;

        $query = Patient::query();

        // Filter by category if healthcare admin has a specific category
        if ($adminCategory && $adminCategory->value !== 'medical_records') {
            $categoryMap = match ($adminCategory->value) {
                'healthcard' => 'health_card',
                'hiv' => 'hiv_testing',
                'pregnancy' => 'pregnancy_care',
                default => null,
            };

            if ($categoryMap) {
                // Filter patients who have appointments in this category
                $query->whereHas('appointments.service', fn ($q) => $q->where('category', $categoryMap));
            }
        }

        return [
            'total_patients' => (clone $query)->count(),
            'male_patients' => (clone $query)->where('gender', 'male')->count(),
            'female_patients' => (clone $query)->where('gender', 'female')->count(),
            'patients_this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $adminCategory = $user->admin_category;

        $patientsQuery = Patient::with(['user', 'barangay', 'appointments'])
            ->orderBy('created_at', 'desc');

        // Filter by admin category
        if ($adminCategory && $adminCategory->value !== 'medical_records') {
            $categoryMap = match ($adminCategory->value) {
                'healthcard' => 'health_card',
                'hiv' => 'hiv_testing',
                'pregnancy' => 'pregnancy_care',
                default => null,
            };

            if ($categoryMap) {
                // Filter patients who have appointments in this category
                $patientsQuery->whereHas('appointments.service', fn ($q) => $q->where('category', $categoryMap));
            }
        }

        // Search filter
        if ($this->search) {
            $patientsQuery->where(function ($query) {
                $query->where('patient_number', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%"))
                    ->orWhereHas('barangay', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            });
        }

        // Gender filter
        if ($this->genderFilter) {
            $patientsQuery->where('gender', $this->genderFilter);
        }

        // Barangay filter
        if ($this->barangayFilter) {
            $patientsQuery->where('barangay_id', $this->barangayFilter);
        }

        $patients = $patientsQuery->paginate(15);

        // Get all barangays for filter
        $barangays = \App\Models\Barangay::orderBy('name')->get();

        return view('livewire.healthcare-admin.patient-list', [
            'patients' => $patients,
            'statistics' => $this->statistics,
            'barangays' => $barangays,
            'adminCategory' => $adminCategory,
        ]);
    }
}
