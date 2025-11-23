<?php

namespace App\Livewire\Admin;

use App\Models\Barangay;
use App\Services\DiseaseSurveillanceService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DiseaseSurveillance extends Component
{
    public ?string $selectedDiseaseType = null;

    public string $selectedPeriod = '30days';

    /** @var int|null Specific selected year when period uses year:YYYY */
    public ?int $selectedYear = null;

    public ?int $selectedBarangayId = null;

    public string $activeTab = 'overview';

    protected DiseaseSurveillanceService $surveillanceService;

    public function boot(DiseaseSurveillanceService $surveillanceService): void
    {
        $this->surveillanceService = $surveillanceService;
    }

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\Disease::class);
    }

    #[Computed]
    public function statistics(): array
    {
        return $this->surveillanceService->getStatistics(
            $this->selectedDiseaseType,
            $this->selectedPeriod
        );
    }

    #[Computed]
    public function heatmapData(): array
    {
        return $this->surveillanceService->getHeatmapData(
            $this->selectedDiseaseType,
            $this->selectedPeriod
        );
    }

    #[Computed]
    public function trendAnalysis(): array
    {
        if (! $this->selectedDiseaseType) {
            return ['historical' => [], 'predicted' => []];
        }

        return $this->surveillanceService->getTrendAnalysis($this->selectedDiseaseType);
    }

    #[Computed]
    public function trendsData(): array
    {
        // Return formatted data for Chart.js
        $analysis = $this->trendAnalysis;
        $data = [];

        // Add historical data
        foreach ($analysis['historical'] as $item) {
            $data[] = [
                'period' => $item['month'],
                'cases_count' => $item['cases'],
                'disease_type' => $this->selectedDiseaseType ?? 'all',
            ];
        }

        return $data;
    }

    #[Computed]
    public function highRiskBarangays(): array
    {
        return $this->surveillanceService->getHighRiskBarangays(
            $this->selectedDiseaseType,
            $this->selectedPeriod
        );
    }

    #[Computed]
    public function outbreakAlerts(): array
    {
        return $this->surveillanceService->detectOutbreaks($this->selectedDiseaseType);
    }

    #[Computed]
    public function diseaseTypes(): array
    {
        return $this->surveillanceService->getAvailableDiseaseTypes();
    }

    #[Computed]
    public function availableYears(): array
    {
        return $this->surveillanceService->getAvailableYears();
    }

    #[Computed]
    public function barangays()
    {
        return Barangay::orderBy('name')->get();
    }

    public function updatedSelectedDiseaseType(): void
    {
        $this->resetFilters(false);
        $this->dispatch('filters-updated', [
            'statistics' => $this->statistics,
            'heatmapData' => $this->heatmapData,
            'trendsData' => $this->trendsData,
        ]);
    }

    public function updatedSelectedPeriod(): void
    {
        // If selecting a specific year period like year:2023, extract and set selectedYear
        if (str_starts_with($this->selectedPeriod, 'year:')) {
            $this->selectedYear = (int) str_replace('year:', '', $this->selectedPeriod);
        } else {
            $this->selectedYear = null;
        }
        unset($this->statistics);
        unset($this->heatmapData);
        $this->dispatch('filters-updated', [
            'statistics' => $this->statistics,
            'heatmapData' => $this->heatmapData,
            'trendsData' => $this->trendsData,
        ]);
    }

    public function updatedSelectedBarangayId(): void
    {
        unset($this->statistics);
        unset($this->heatmapData);
        $this->dispatch('filters-updated', [
            'statistics' => $this->statistics,
            'heatmapData' => $this->heatmapData,
            'trendsData' => $this->trendsData,
        ]);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tab-changed', tab: $tab);
    }

    public function resetFilters(bool $all = true): void
    {
        if ($all) {
            $this->selectedDiseaseType = null;
        }
        $this->selectedPeriod = '30days';
        $this->selectedYear = null;
        $this->selectedBarangayId = null;

        // Clear computed properties cache
        unset($this->statistics);
        unset($this->heatmapData);
        unset($this->trendAnalysis);
        unset($this->highRiskBarangays);
    }

    public function render()
    {
        return view('livewire.admin.disease-surveillance');
    }
}
