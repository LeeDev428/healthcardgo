<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Doctor Dashboard')]
class DoctorDashboard extends Component
{
    public $searchPatient = '';

    public $selectedPatientId = null;

    public $currentAppointmentId = null;

    public $showPatientModal = false;

    public ?Doctor $doctor = null;

    public function mount(): void
    {
        // Cache the authenticated doctor's profile (may be null if not provisioned yet)
        $this->doctor = Auth::user()->doctor;

        if ($this->doctor) {
            // Get current appointment in progress if any
            $this->currentAppointmentId = Appointment::where('doctor_id', $this->doctor->id)
                ->where('status', 'in_progress')
                ->value('id');
        } else {
            $this->currentAppointmentId = null;
        }
    }

    #[On('appointment-checked-in')]
    #[On('appointment-started')]
    #[On('appointment-completed')]
    public function refreshDashboard(): void
    {
        // Refresh component data
        $this->mount();
    }

    public function checkInPatient($appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);

        if (! $appointment->canCheckIn()) {
            $this->dispatch('error', message: 'This appointment cannot be checked in yet.');

            return;
        }

        $appointment->checkIn();

        $this->dispatch('appointment-checked-in');
        $this->dispatch('success', message: 'Patient checked in successfully.');
    }

    public function startConsultation($appointmentId): void
    {
        // Complete any existing in-progress consultation first
        if ($this->currentAppointmentId && $this->currentAppointmentId !== $appointmentId) {
            $this->dispatch('error', message: 'Please complete the current consultation first.');

            return;
        }

        $appointment = Appointment::findOrFail($appointmentId);

        if ($appointment->status !== 'checked_in') {
            $this->dispatch('error', message: 'Patient must be checked in first.');

            return;
        }

        $appointment->start();
        $this->currentAppointmentId = $appointmentId;

        $this->dispatch('appointment-started');
        $this->dispatch('success', message: 'Consultation started.');
    }

    public function viewPatient($patientId): void
    {
        $this->selectedPatientId = $patientId;
        $this->showPatientModal = true;
    }

    public function closePatientModal(): void
    {
        $this->showPatientModal = false;
        $this->selectedPatientId = null;
    }

    public function searchPatients()
    {
        if (strlen($this->searchPatient) < 2) {
            return collect();
        }

        return Patient::with(['user', 'barangay'])
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchPatient.'%')
                        ->orWhere('email', 'like', '%'.$this->searchPatient.'%');
                })
                    ->orWhere('contact_number', 'like', '%'.$this->searchPatient.'%');
            })
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $doctor = $this->doctor ?? Auth::user()->doctor;

        // If the doctor profile is missing, provide safe empty data to the view.
        if (! $doctor) {
            return view('livewire.doctor.doctor-dashboard', [
                'todayAppointments' => collect(),
                'currentAppointment' => null,
                'stats' => [
                    'today_total' => 0,
                    'completed_today' => 0,
                    'in_queue' => 0,
                    'no_shows' => 0,
                    'patients_seen_week' => 0,
                    'pending_records' => 0,
                ],
                'upcomingAppointments' => collect(),
                'recentCompleted' => collect(),
                'searchResults' => collect(),
                'selectedPatient' => null,
                'doctorMissing' => true,
            ]);
        }

        // Today's appointments
        $todayAppointments = Appointment::with(['patient.user', 'patient.barangay', 'service'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('scheduled_at', today())
            ->orderBy('queue_number')
            ->get();

        // Current appointment in progress
        $currentAppointment = $this->currentAppointmentId
            ? Appointment::with(['patient.user', 'patient.barangay', 'service'])->find($this->currentAppointmentId)
            : null;

        // Statistics
        $stats = [
            'today_total' => $todayAppointments->count(),
            'completed_today' => $todayAppointments->where('status', 'completed')->count(),
            'in_queue' => $todayAppointments->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'no_shows' => $todayAppointments->where('status', 'no_show')->count(),
            'patients_seen_week' => Appointment::where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->distinct('patient_id')
                ->count('patient_id'),
            'pending_records' => MedicalRecord::where('doctor_id', $doctor->id)
                ->whereNull('updated_at')
                ->orWhere('updated_at', '<', now()->subHours(24))
                ->count(),
        ];

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::with(['patient.user', 'service'])
            ->where('doctor_id', $doctor->id)
            ->whereBetween('scheduled_at', [now()->addDay(), now()->addDays(7)])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        // Recent completed appointments
        $recentCompleted = Appointment::with(['patient.user', 'service'])
            ->where('doctor_id', $doctor->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Patient search results
        $searchResults = $this->searchPatients();

        // Selected patient details
        $selectedPatient = $this->selectedPatientId
            ? Patient::with(['user', 'barangay', 'appointments.service', 'medicalRecords'])->find($this->selectedPatientId)
            : null;

        return view('livewire.doctor.doctor-dashboard', [
            'todayAppointments' => $todayAppointments,
            'currentAppointment' => $currentAppointment,
            'stats' => $stats,
            'upcomingAppointments' => $upcomingAppointments,
            'recentCompleted' => $recentCompleted,
            'searchResults' => $searchResults,
            'selectedPatient' => $selectedPatient,
            'doctorMissing' => false,
        ]);
    }
}
