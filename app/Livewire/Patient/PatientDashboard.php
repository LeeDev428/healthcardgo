<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Models\HealthCard;
use App\Models\MedicalRecord;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('My Dashboard')]
class PatientDashboard extends Component
{
    public function render()
    {
        $patient = Auth::user()->patient;

        // Check if patient profile exists
        if (! $patient) {
            return view('livewire.patient.patient-dashboard', [
                'upcomingAppointments' => collect([]),
                'nextAppointment' => null,
                'recentAppointments' => collect([]),
                'healthCard' => null,
                'recentMedicalRecords' => collect([]),
                'stats' => [
                    'total_appointments' => 0,
                    'completed_appointments' => 0,
                    'upcoming_appointments' => 0,
                    'unread_notifications' => Notification::where('user_id', Auth::id())
                        ->whereNull('read_at')
                        ->count(),
                    'medical_records' => 0,
                    'health_card_status' => 'No Card',
                ],
                'profileIncomplete' => true,
            ]);
        }

        // Upcoming appointments
        $upcomingAppointments = Appointment::with(['service', 'doctor.user'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        // Next appointment
        $nextAppointment = $upcomingAppointments->first();

        // Recent appointments
        $recentAppointments = Appointment::with(['service', 'doctor.user'])
            ->where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Health card
        $healthCard = HealthCard::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->first();

        // Recent medical records
        $recentMedicalRecords = MedicalRecord::with(['doctor.user'])
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Unread notifications count
        $unreadNotifications = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        // Statistics
        $stats = [
            'total_appointments' => Appointment::where('patient_id', $patient->id)->count(),
            'completed_appointments' => Appointment::where('patient_id', $patient->id)
                ->where('status', 'completed')
                ->count(),
            'upcoming_appointments' => $upcomingAppointments->count(),
            'unread_notifications' => $unreadNotifications,
            'medical_records' => MedicalRecord::where('patient_id', $patient->id)->count(),
            'health_card_status' => $healthCard ? 'Active' : 'No Card',
        ];

        return view('livewire.patient.patient-dashboard', [
            'upcomingAppointments' => $upcomingAppointments,
            'nextAppointment' => $nextAppointment,
            'recentAppointments' => $recentAppointments,
            'healthCard' => $healthCard,
            'recentMedicalRecords' => $recentMedicalRecords,
            'stats' => $stats,
            'profileIncomplete' => false,
        ]);
    }
}
