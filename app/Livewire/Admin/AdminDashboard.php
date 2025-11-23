<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Disease;
use App\Models\Doctor;
use App\Models\Feedback;
use App\Models\HealthCard;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
class AdminDashboard extends Component
{
    public function render()
    {
        // Get key metrics with growth indicators
        $stats = [
            'total_patients' => Patient::count(),
            'total_patients_growth' => $this->calculateGrowth(Patient::class, 30),
            'total_doctors' => Doctor::count(),
            'total_appointments' => Appointment::count(),
            'total_appointments_growth' => $this->calculateGrowth(Appointment::class, 30),
            'active_health_cards' => HealthCard::where('status', 'active')->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'today_appointments' => Appointment::whereDate('scheduled_at', today())->count(),
            'completed_today' => Appointment::whereDate('completed_at', today())->count(),
            'disease_cases' => Disease::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'pending_approvals' => User::where('status', 'pending')->count(),
            'average_feedback_rating' => round(Feedback::avg('overall_rating'), 1) ?? 0,
            'total_feedback' => Feedback::count(),
        ];

        // Appointment status breakdown
        $appointmentStatusBreakdown = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Recent activities
        $recentAppointments = Appointment::with(['patient.user', 'service', 'doctor'])
            ->latest()
            ->limit(5)
            ->get();

        $recentHealthCards = HealthCard::with(['patient.user'])
            ->latest()
            ->limit(5)
            ->get();

        $recentFeedback = Feedback::with(['patient.user'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingApprovals = User::with('patient')
            ->where('status', 'pending')
            ->where('role_id', 4) // Patients
            ->latest()
            ->limit(5)
            ->get();

        // Weekly appointment trend (last 7 days)
        $weeklyTrend = Appointment::selectRaw('DATE(scheduled_at) as date, COUNT(*) as count')
            ->where('scheduled_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing days with 0
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            if (! isset($weeklyTrend[$date])) {
                $weeklyTrend[$date] = 0;
            }
        }
        ksort($weeklyTrend);

        // Top services by appointment count
        $topServices = Appointment::select('service_id', DB::raw('count(*) as count'))
            ->with('service')
            ->groupBy('service_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'appointmentStatusBreakdown' => $appointmentStatusBreakdown,
            'recentAppointments' => $recentAppointments,
            'recentHealthCards' => $recentHealthCards,
            'recentFeedback' => $recentFeedback,
            'pendingApprovals' => $pendingApprovals,
            'weeklyTrend' => $weeklyTrend,
            'topServices' => $topServices,
        ]);
    }

    /**
     * Calculate growth percentage compared to previous period
     */
    protected function calculateGrowth($model, int $days): float
    {
        $currentPeriod = $model::where('created_at', '>=', now()->subDays($days))->count();
        $previousPeriod = $model::whereBetween('created_at', [
            now()->subDays($days * 2),
            now()->subDays($days),
        ])->count();

        if ($previousPeriod == 0) {
            return $currentPeriod > 0 ? 100 : 0;
        }

        return round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 1);
    }
}
