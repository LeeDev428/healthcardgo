<?php

namespace App\Livewire\HealthcareAdmin;

use App\Enums\AdminCategoryEnum;
use App\Models\Service;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Reports extends Component
{
    public string $type = 'appointments';

    public ?string $from = null;

    public ?string $to = null;

    public ?string $status = null;

    public ?int $doctor_id = null;

    public ?string $service_category = null;

    public ?string $disease_type = null;

    public ?int $barangay_id = null;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();

        // Don't prefill service_category to avoid over-filtering; rely on backend scoping
        $adminCategory = Auth::user()?->admin_category;
        $this->service_category = null;
        if ($adminCategory === AdminCategoryEnum::HIV) {
            $this->disease_type = 'hiv';
        } elseif ($adminCategory === AdminCategoryEnum::Pregnancy) {
            $this->disease_type = 'pregnancy_complications';
        }
    }

    protected function mapAdminCategoryToServiceCategory(?AdminCategoryEnum $adminCategory): ?string
    {
        return match ($adminCategory) {
            AdminCategoryEnum::HealthCard => 'health_card',
            AdminCategoryEnum::HIV => 'hiv_testing',
            AdminCategoryEnum::Pregnancy => 'pregnancy_care',
            default => null,
        };
    }

    public function getDatasetProperty(): array
    {
        /** @var ReportService $svc */
        $svc = app(ReportService::class);

        $filters = [
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'doctor_id' => $this->doctor_id,
            'service_category' => $this->service_category,
            'disease_type' => $this->disease_type,
            'barangay_id' => $this->barangay_id,
        ];

        return match ($this->type) {
            'diseases' => $svc->getDiseasesReport($filters, Auth::user()),
            'feedback' => $svc->getFeedbackReport($filters, Auth::user()),
            default => $svc->getAppointmentsReport($filters, Auth::user()),
        };
    }

    public function resetFilters(): void
    {
        $this->status = null;
        $this->doctor_id = null;
        $this->disease_type = null;
        $this->barangay_id = null;
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();

        // Keep service_category cleared; scoping is handled server-side
        $this->service_category = null;
    }

    public function render()
    {
        $doctors = User::whereHas('role', fn ($q) => $q->where('name', 'doctor'))
            ->with('role')
            ->orderBy('name')
            ->get(['id', 'name']);

        $serviceCategories = Service::getCategories();

        $barangays = \App\Models\Barangay::orderBy('name')->get(['id', 'name']);

        return view('livewire.healthcare-admin.reports', [
            'dataset' => $this->dataset,
            'doctors' => $doctors,
            'serviceCategories' => $serviceCategories,
            'barangays' => $barangays,
            'adminCategory' => Auth::user()?->admin_category?->label() ?? null,
        ]);
    }
}
