<?php

namespace App\Livewire\HealthcareAdmin;

use App\Enums\AdminCategoryEnum;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HealthcareAdminDashboard extends Component
{
    /**
     * Get appointment trend and prediction data for healthcard appointments
     */
    protected function getHealthcardAppointmentTrends(): array
    {
        return $this->getAppointmentTrends('health_card');
    }

    /**
     * Get appointment trend and prediction data for HIV appointments
     */
    protected function getHivAppointmentTrends(): array
    {
        return $this->getAppointmentTrends('hiv_testing');
    }

    /**
     * Get appointment trend and prediction data for pregnancy appointments
     */
    protected function getPregnancyAppointmentTrends(): array
    {
        return $this->getAppointmentTrends('pregnancy_care');
    }

    /**
     * Get appointment trend and prediction data for a specific service category
     */
    protected function getAppointmentTrends(string $category): array
    {
        // Get 12 months of historical data starting from 12 months ago
        $currentMonth = now()->startOfMonth();
        $startMonth = $currentMonth->copy()->subMonths(11); // 11 months ago + current month = 12 months

        $months = collect();
        for ($i = 0; $i < 12; $i++) {
            $months->push($startMonth->copy()->addMonths($i));
        }

        // Get historical data
        $noShowData = [];
        $completedData = [];
        $cancelledData = [];
        $labels = [];

        foreach ($months as $month) {
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();

            $labels[] = $month->format('M Y');

            // Count appointments by status for specified category
            $baseQuery = Appointment::whereHas('service', function ($q) use ($category) {
                $q->where('category', $category);
            })->whereBetween('scheduled_at', [$startDate, $endDate]);

            $noShowData[] = (clone $baseQuery)->where('status', 'no_show')->count();
            $completedData[] = (clone $baseQuery)->whereIn('status', ['confirmed', 'cancelled'])->count();
            $cancelledData[] = (clone $baseQuery)->where('status', 'cancelled')->count();
        }

        // Add predictions for the next two months from current month
        $nextMonth1 = now()->addMonth()->startOfMonth();
        $nextMonth2 = now()->addMonths(2)->startOfMonth();

        // Predict for next month (month 1)
        $predictedNoShow1 = $this->predictNextValue($noShowData);
        $predictedCompleted1 = $this->predictNextValue($completedData);
        $predictedCancelled1 = $this->predictNextValue($cancelledData);

        $labels[] = $nextMonth1->format('M Y').' (Predicted)';
        $noShowData[] = $predictedNoShow1;
        $completedData[] = $predictedCompleted1;
        $cancelledData[] = $predictedCancelled1;

        // Predict for second month (month 2) based on updated data including first prediction
        $predictedNoShow2 = $this->predictNextValue($noShowData);
        $predictedCompleted2 = $this->predictNextValue($completedData);
        $predictedCancelled2 = $this->predictNextValue($cancelledData);

        $labels[] = $nextMonth2->format('M Y').' (Predicted)';
        $noShowData[] = $predictedNoShow2;
        $completedData[] = $predictedCompleted2;
        $cancelledData[] = $predictedCancelled2;

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'No Show',
                    'data' => $noShowData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Confirmed',
                    'data' => $completedData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Cancelled',
                    'data' => $cancelledData,
                    'borderColor' => 'rgb(251, 146, 60)',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.5)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
        ];
    }

    /**
     * Simple linear prediction based on recent trend
     */
    protected function predictNextValue(array $data): int
    {
        $count = count($data);
        if ($count < 2) {
            return end($data) ?: 0;
        }

        // Use last 3 months for trend if available
        $recentData = array_slice($data, -3);
        $avg = array_sum($recentData) / count($recentData);

        // Calculate simple trend
        $last = end($data);
        $secondLast = $data[$count - 2] ?? $last;

        // If increasing, add difference, otherwise use average
        $trend = $last - $secondLast;
        $prediction = max(0, round($last + ($trend * 0.7))); // 70% of trend

        return (int) $prediction;
    }

    public function render()
    {
        $user = Auth::user();
        $adminCategory = $user->admin_category?->value;

        // Base statistics - filter pending_appointments by admin category
        $pendingAppointmentsQuery = Appointment::where('status', 'pending');
        
        if ($user->role_id === 2 && $user->admin_category && $user->admin_category !== AdminCategoryEnum::MedicalRecords) {
            $categoryMap = match ($user->admin_category) {
                AdminCategoryEnum::HealthCard => 'health_card',
                AdminCategoryEnum::HIV => 'hiv_testing',
                AdminCategoryEnum::Pregnancy => 'pregnancy_care',
                default => null,
            };
            
            if ($categoryMap) {
                $pendingAppointmentsQuery->whereHas('service', function ($q) use ($categoryMap) {
                    $q->where('category', $categoryMap);
                });
            }
        }
        
        $statistics = [
            'pending_approvals' => User::where('status', 'pending')->where('role_id', 4)->count(),
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('scheduled_at', today())->count(),
            'pending_appointments' => $pendingAppointmentsQuery->count(),
            'total_healthcard_patients' => \App\Models\HealthCard::distinct('patient_id')->count('patient_id'),
        ];
        
        // Override total_patients for category-specific admins
        if ($user->role_id === 2 && $user->admin_category && $user->admin_category !== AdminCategoryEnum::MedicalRecords) {
            $categoryMap = match ($user->admin_category) {
                AdminCategoryEnum::HealthCard => 'health_card',
                AdminCategoryEnum::HIV => 'hiv_testing',
                AdminCategoryEnum::Pregnancy => 'pregnancy_care',
                default => null,
            };
            
            if ($categoryMap) {
                // Count distinct patients who have appointments in this category
                $statistics['total_patients'] = Patient::whereHas('appointments.service', function ($q) use ($categoryMap) {
                    $q->where('category', $categoryMap);
                })->distinct()->count('id');
            }
        }

        // Appointment trends based on admin category
        $appointmentTrends = null;
        if (in_array(strtolower($adminCategory ?? ''), ['healthcard', 'healthcard admin'])) {
            $appointmentTrends = $this->getHealthcardAppointmentTrends();
        } elseif (in_array(strtolower($adminCategory ?? ''), ['hiv', 'hiv admin'])) {
            $appointmentTrends = $this->getHivAppointmentTrends();
        } elseif (in_array(strtolower($adminCategory ?? ''), ['pregnancy', 'pregnancy admin'])) {
            $appointmentTrends = $this->getPregnancyAppointmentTrends();
        }

        // Category-specific statistics based on admin category
        if ($adminCategory) {
            switch (strtolower($adminCategory)) {
                case 'healthcard':
                case 'healthcard admin':
                    $statistics['healthcard_patients'] = Patient::has('healthCards')->count();
                    $statistics['active_healthcards'] = \App\Models\HealthCard::where('status', 'active')->count();
                    break;

                case 'hiv':
                case 'hiv admin':
                    // HIV-specific statistics
                    $statistics['hiv_appointments'] = Appointment::whereHas('service', function ($q) {
                        $q->where('category', 'hiv_testing');
                    })->count();
                    break;

                case 'pregnancy':
                case 'pregnancy admin':
                    // Pregnancy-specific statistics
                    $statistics['pregnancy_appointments'] = Appointment::whereHas('service', function ($q) {
                        $q->where('category', 'pregnancy_care');
                    })->count();
                    break;

                case 'medical records':
                case 'medical records admin':
                    $statistics['total_records'] = \App\Models\MedicalRecord::count();
                    $statistics['records_this_month'] = \App\Models\MedicalRecord::whereMonth('created_at', now()->month)->count();
                    break;
            }
        }

        // Recent appointments (limited by category if applicable)
        $appointmentsQuery = Appointment::with(['patient.user', 'doctor.user', 'service'])
            ->where('scheduled_at', '>=', today())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->orderBy('scheduled_at');

        if ($adminCategory) {
            $category = match (strtolower($adminCategory)) {
                'hiv', 'hiv admin' => 'hiv_testing',
                'pregnancy', 'pregnancy admin' => 'pregnancy_care',
                'healthcard', 'healthcard admin' => 'health_card',
                default => null,
            };

            if ($category) {
                $appointmentsQuery->whereHas('service', function ($q) use ($category) {
                    $q->where('category', $category);
                });
            }
        }

        $upcomingAppointments = $appointmentsQuery->take(10)->get();

        // Pending patient approvals
        $pendingPatients = User::where('status', 'pending')
            ->where('role_id', 4)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get();

        // Recent health card patients (for healthcard admins)
        $recentHealthCardPatients = null;
        if (in_array(strtolower($adminCategory ?? ''), ['healthcard', 'healthcard admin'])) {
            $recentHealthCardPatients = \App\Models\HealthCard::with(['patient.user', 'patient.barangay'])
                ->where('status', 'active')
                ->latest('created_at')
                ->take(10)
                ->get();
        }

        return view('livewire.healthcare-admin.healthcare-admin-dashboard', [
            'statistics' => $statistics,
            'upcomingAppointments' => $upcomingAppointments,
            'pendingPatients' => $pendingPatients,
            'adminCategory' => $adminCategory,
            'appointmentTrends' => $appointmentTrends,
            'recentHealthCardPatients' => $recentHealthCardPatients,
        ]);
    }
}
