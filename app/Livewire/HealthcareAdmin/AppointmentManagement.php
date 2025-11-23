<?php

namespace App\Livewire\HealthcareAdmin;

use App\Enums\AdminCategoryEnum;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $dateFilter = '';

    public $serviceFilter = '';

    public $selectedAppointment = null;

    public $showDetailsModal = false;

    public $showAssignDoctorModal = false;

    public $selectedDoctorId = null;

    // Status update modal state
    public $showStatusModal = false;

    public $statusForm = [
        'appointment_id' => null,
        'to' => '',
        'reason' => '',
    ];

    /**
     * The list of available next statuses for the selected appointment.
     *
     * @var array<int,string>
     */
    public $availableStatuses = [];

    public function mount(): void
    {
        // Set default date filter to today
        // $this->dateFilter = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingServiceFilter(): void
    {
        $this->resetPage();
    }

    public function viewDetails(int $appointmentId): void
    {
        $this->selectedAppointment = Appointment::with(['patient.user', 'service', 'doctor.user'])
            ->find($appointmentId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedAppointment = null;
    }

    public function openAssignDoctorModal(int $appointmentId): void
    {
        $this->selectedAppointment = Appointment::with(['patient.user', 'service'])
            ->find($appointmentId);
        $this->selectedDoctorId = $this->selectedAppointment->doctor_id;
        $this->showAssignDoctorModal = true;
    }

    public function closeAssignDoctorModal(): void
    {
        $this->showAssignDoctorModal = false;
        $this->selectedAppointment = null;
        $this->selectedDoctorId = null;
    }

    public function assignDoctor(): void
    {
        $this->validate([
            'selectedDoctorId' => 'required|exists:users,id',
        ]);

        $this->selectedAppointment->update([
            'doctor_id' => $this->selectedDoctorId,
            'status' => 'confirmed',
        ]);

        // Ensure QR and digital copy are generated upon confirmation
        app(\App\Services\AppointmentService::class)
            ->ensureAssetsGenerated($this->selectedAppointment->fresh());

        // Notify patient that appointment has been confirmed
        /** @var NotificationService $notifier */
        $notifier = app(NotificationService::class);
        $notifier->sendAppointmentConfirmation($this->selectedAppointment->fresh()->loadMissing(['service', 'patient']));

        $this->closeAssignDoctorModal();

        session()->flash('message', 'Doctor assigned successfully.');
    }

    public function openStatusModal(int $appointmentId): void
    {
        $appointment = Appointment::with(['patient.user', 'service'])->findOrFail($appointmentId);
        $this->selectedAppointment = $appointment;
        $this->statusForm = [
            'appointment_id' => $appointment->id,
            'to' => '',
            'reason' => '',
        ];
        $this->availableStatuses = $this->computeAvailableStatuses($appointment);
        $this->showStatusModal = true;
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->statusForm = [
            'appointment_id' => null,
            'to' => '',
            'reason' => '',
        ];
        $this->availableStatuses = [];
        $this->selectedAppointment = null;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'statusForm.appointment_id' => 'required|exists:appointments,id',
            'statusForm.to' => 'required|string|in:pending,confirmed,checked_in,in_progress,completed,cancelled,no_show',
            'statusForm.reason' => 'nullable|string|max:1000',
        ], [], [
            'statusForm.appointment_id' => 'appointment',
            'statusForm.to' => 'status',
            'statusForm.reason' => 'cancellation reason',
        ]);

        $appointment = Appointment::findOrFail($this->statusForm['appointment_id']);
        $to = $this->statusForm['to'];

        // Enforce allowed transitions
        $allowed = $this->computeAvailableStatuses($appointment);
        if (! in_array($to, $allowed, true)) {
            $this->addError('statusForm.to', 'This status change is not allowed from the current state.');

            return;
        }

        // If cancelling, require a reason
        if ($to === 'cancelled') {
            $this->validate([
                'statusForm.reason' => 'required|string|min:3|max:1000',
            ]);
            // Use AppointmentService to ensure queue recalculation & notifications
            app(\App\Services\AppointmentService::class)->cancelAppointment($appointment, $this->statusForm['reason']);
        } elseif ($to === 'completed') {
            // Only from in_progress; handled by allowed transitions
            $appointment->complete();
            // Notify patient to leave feedback after completion
            /** @var NotificationService $notifier */
            $notifier = app(NotificationService::class);
            $notifier->sendFeedbackRequest($appointment->fresh());
        } elseif ($to === 'checked_in') {
            $appointment->checkIn();
            // Notify doctor patient has checked in
            // Centralized check-in logic + notification
            app(\App\Services\AppointmentService::class)->checkIn($appointment);
        } elseif ($to === 'in_progress') {
            $appointment->start();
        } else {
            // pending, confirmed, no_show direct updates
            $appointment->update(['status' => $to]);
            if ($to === 'confirmed') {
                // Ensure assets exist for confirmed appointments
                app(\App\Services\AppointmentService::class)->ensureAssetsGenerated($appointment->fresh());
            }
            // If status transitioned to confirmed here, notify patient
            if ($to === 'confirmed') {
                /** @var NotificationService $notifier */
                $notifier = app(NotificationService::class);
                $notifier->sendAppointmentConfirmation($appointment->fresh()->loadMissing(['service', 'patient']));
            }
        }

        $this->closeStatusModal();
        session()->flash('message', 'Appointment status updated to '.ucfirst(str_replace('_', ' ', $to)).'.');
    }

    /**
     * Compute allowed next statuses based on current appointment status.
     *
     * Rules:
     * - pending -> confirmed, cancelled
     * - confirmed -> checked_in, cancelled, no_show
     * - checked_in -> in_progress, cancelled, no_show
     * - in_progress -> completed
     * - completed -> [] (terminal)
     * - cancelled -> [] (terminal)
     * - no_show -> [] (terminal)
     */
    protected function computeAvailableStatuses(Appointment $appointment): array
    {
        return match ($appointment->status) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['checked_in', 'completed', 'cancelled', 'no_show'],
            'checked_in' => ['in_progress', 'completed', 'cancelled', 'no_show'],
            'in_progress' => ['completed'],
            default => [], // completed, cancelled, no_show
        };
    }

    public function checkInAppointment(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment && $appointment->canCheckIn()) {
            $appointment->checkIn();
            session()->flash('message', 'Patient checked in successfully.');
        }
    }

    public function startAppointment(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment && $appointment->status === 'checked_in') {
            $appointment->start();
            session()->flash('message', 'Appointment started.');
        }
    }

    public function completeAppointment(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment && $appointment->status === 'in_progress') {
            $appointment->complete();
            session()->flash('message', 'Appointment completed successfully.');
        }
    }

    public function markNoShow(int $appointmentId): void
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment) {
            $appointment->update(['status' => 'no_show']);
            session()->flash('message', 'Appointment marked as no-show.');
        }
    }

    public function getStatisticsProperty(): array
    {
        $user = Auth::user();

        $query = Appointment::query();

        // Filter by category if healthcare admin
        if ($user->role_id === 2) {
            $adminCategory = $user->admin_category;
            
            // Medical Records Admin sees ALL appointments stats
            if ($adminCategory !== AdminCategoryEnum::MedicalRecords) {
                $categoryMap = match ($adminCategory) {
                    AdminCategoryEnum::HealthCard => 'health_card',
                    AdminCategoryEnum::HIV => 'hiv_testing',
                    AdminCategoryEnum::Pregnancy => 'pregnancy_care',
                    default => null,
                };

                if ($categoryMap) {
                    $query->whereHas('service', function ($q) use ($categoryMap) {
                        $q->where('category', $categoryMap);
                    });
                }
            }
        }

        return [
            'total_today' => (clone $query)->whereDate('scheduled_at', today())->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'checked_in' => (clone $query)->where('status', 'checked_in')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed_today' => (clone $query)->whereDate('scheduled_at', today())->where('status', 'completed')->count(),
            'no_show_today' => (clone $query)->whereDate('scheduled_at', today())->where('status', 'no_show')->count(),
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $adminCategory = $user->admin_category;
        
        $appointmentsQuery = Appointment::with(['patient.user', 'service', 'doctor.user'])
            ->orderBy('scheduled_at', 'desc');

        // ONLY filter if NOT Medical Records Admin
        // Medical Records sees ALL, others see their category only
        if ($user->role_id === 2 && $adminCategory && $adminCategory !== AdminCategoryEnum::MedicalRecords) {
            $categoryMap = match ($adminCategory) {
                AdminCategoryEnum::HealthCard => 'health_card',
                AdminCategoryEnum::HIV => 'hiv_testing',
                AdminCategoryEnum::Pregnancy => 'pregnancy_care',
                default => null,
            };

            if ($categoryMap) {
                $appointmentsQuery->whereHas('service', function ($q) use ($categoryMap) {
                    $q->where('category', $categoryMap);
                });
            }
        }

        // Search filter
        if ($this->search) {
            $appointmentsQuery->where(function ($query) {
                $query->where('appointment_number', 'like', "%{$this->search}%")
                    ->orWhereHas('patient.user', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('service', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $appointmentsQuery->where('status', $this->statusFilter);
        }

        // Date filter
        if ($this->dateFilter) {
            $appointmentsQuery->whereDate('scheduled_at', $this->dateFilter);
        }

        // Service filter
        if ($this->serviceFilter) {
            $appointmentsQuery->where('service_id', $this->serviceFilter);
        }

        $appointments = $appointmentsQuery->paginate(15);

        // Get available services for filter
        $servicesQuery = Service::active()->orderBy('name');
        if ($adminCategory && $adminCategory !== AdminCategoryEnum::MedicalRecords) {
            $categoryMap = match ($adminCategory) {
                AdminCategoryEnum::HealthCard => 'health_card',
                AdminCategoryEnum::HIV => 'hiv_testing',
                AdminCategoryEnum::Pregnancy => 'pregnancy_care',
                default => null,
            };

            if ($categoryMap) {
                $servicesQuery->where('category', $categoryMap);
            }
        }
        $services = $servicesQuery->get();

        // Get available doctors (users with doctor role)
        $doctors = User::whereHas('role', fn ($q) => $q->where('name', 'doctor'))
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('livewire.healthcare-admin.appointment-management', [
            'appointments' => $appointments,
            'statistics' => $this->statistics,
            'services' => $services,
            'doctors' => $doctors,
            'adminCategory' => $adminCategory,
        ]);
    }
}
