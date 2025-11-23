<?php

namespace App\Livewire\Admin;

use App\Models\Barangay;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Patient Management')]
class PatientManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $barangayFilter = 'all';

    public $selectedPatientId = null;

    public $showDetailsModal = false;

    public $showEditModal = false;

    public $editForm = [
        'name' => '',
        'email' => '',
        'contact_number' => '',
        'barangay_id' => '',
        'blood_type' => '',
        'date_of_birth' => '',
        'gender' => '',
        'address' => '',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingBarangayFilter()
    {
        $this->resetPage();
    }

    public function viewDetails($patientId)
    {
        $this->selectedPatientId = $patientId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPatientId = null;
    }

    public function editPatient($patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);

        $this->selectedPatientId = $patientId;
        $this->editForm = [
            'name' => $patient->full_name,
            'email' => $patient->user?->email ?? '',
            'contact_number' => $patient->contact_number,
            'barangay_id' => $patient->barangay_id,
            'blood_type' => $patient->blood_type,
            'date_of_birth' => $patient->date_of_birth,
            'gender' => $patient->gender,
            'address' => $patient->address,
        ];

        $this->showEditModal = true;
    }

    public function updatePatient()
    {
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.email' => 'required|email|max:255',
            'editForm.contact_number' => 'required|string|max:20',
            'editForm.barangay_id' => 'required|exists:barangays,id',
            'editForm.blood_type' => 'nullable|string|max:10',
            'editForm.date_of_birth' => 'nullable|date',
            'editForm.gender' => 'required|in:male,female,other',
            'editForm.address' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () {
                $patient = Patient::findOrFail($this->selectedPatientId);

                // Update user
                $patient->user->update([
                    'name' => $this->editForm['name'],
                    'email' => $this->editForm['email'],
                ]);

                // Update patient
                $patient->update([
                    'contact_number' => $this->editForm['contact_number'],
                    'barangay_id' => $this->editForm['barangay_id'],
                    'blood_type' => $this->editForm['blood_type'],
                    'date_of_birth' => $this->editForm['date_of_birth'],
                    'gender' => $this->editForm['gender'],
                    'address' => $this->editForm['address'],
                ]);
            });

            $this->dispatch('success', message: 'Patient updated successfully.');
            $this->showEditModal = false;
            $this->selectedPatientId = null;
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to update patient: '.$e->getMessage());
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedPatientId = null;
        $this->reset('editForm');
    }

    public function deactivatePatient($patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $patient->user->update(['status' => 'inactive']);

            $this->dispatch('success', message: 'Patient deactivated successfully.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to deactivate patient.');
        }
    }

    public function activatePatient($patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $patient->user->update(['status' => 'approved']);

            $this->dispatch('success', message: 'Patient activated successfully.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to activate patient.');
        }
    }

    public function render()
    {
        $query = Patient::with(['user', 'barangay', 'appointments', 'healthCards', 'medicalRecords']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('contact_number', 'like', '%'.$this->search.'%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->whereHas('user', function ($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        // Barangay filter
        if ($this->barangayFilter !== 'all') {
            $query->where('barangay_id', $this->barangayFilter);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get barangays for filter
        $barangays = Barangay::orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => Patient::count(),
            'active' => Patient::whereHas('user', function ($q) {
                $q->where('status', 'approved');
            })->count(),
            'inactive' => Patient::whereHas('user', function ($q) {
                $q->where('status', 'inactive');
            })->count(),
            'with_appointments' => Patient::has('appointments')->count(),
            'with_health_cards' => Patient::has('healthCards')->count(),
            'new_this_month' => Patient::whereMonth('created_at', now()->month)->count(),
        ];

        // Selected patient details
        $selectedPatient = $this->selectedPatientId
            ? Patient::with(['user', 'barangay', 'appointments.service', 'healthCards', 'medicalRecords.doctor.user'])->find($this->selectedPatientId)
            : null;

        return view('livewire.admin.patient-management', [
            'patients' => $patients,
            'barangays' => $barangays,
            'stats' => $stats,
            'selectedPatient' => $selectedPatient,
        ]);
    }
}
