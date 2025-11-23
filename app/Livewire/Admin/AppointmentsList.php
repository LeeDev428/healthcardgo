<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Service;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Appointments Management')]
class AppointmentsList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $serviceFilter = 'all';

    public $dateFilter = 'all';

    public $selectedAppointmentId = null;

    public $showDetailsModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingServiceFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function viewDetails($appointmentId)
    {
        $this->selectedAppointmentId = $appointmentId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedAppointmentId = null;
    }

    public function render()
    {
        $query = Appointment::with(['patient.user', 'patient.barangay', 'service', 'doctor.user']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('patient.user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('appointment_number', 'like', '%'.$this->search.'%')
                    ->orWhere('queue_number', 'like', '%'.$this->search.'%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Service filter
        if ($this->serviceFilter !== 'all') {
            $query->where('service_id', $this->serviceFilter);
        }

        // Date filter
        if ($this->dateFilter !== 'all') {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('scheduled_at', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('scheduled_at', now()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'next_week':
                    $query->whereBetween('scheduled_at', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('scheduled_at', now()->month)
                        ->whereYear('scheduled_at', now()->year);
                    break;
            }
        }

        $appointments = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        // Get services for filter
        $services = Service::where('is_active', true)->get();

        // Statistics
        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'today' => Appointment::whereDate('scheduled_at', today())->count(),
        ];

        // Selected appointment details
        $selectedAppointment = $this->selectedAppointmentId
            ? Appointment::with(['patient.user', 'patient.barangay', 'service', 'doctor.user'])->find($this->selectedAppointmentId)
            : null;

        return view('livewire.admin.appointments-list', [
            'appointments' => $appointments,
            'services' => $services,
            'stats' => $stats,
            'selectedAppointment' => $selectedAppointment,
        ]);
    }
}
