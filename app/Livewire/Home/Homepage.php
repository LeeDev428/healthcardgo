<?php

namespace App\Livewire\Home;

use App\Models\Announcement;
use App\Services\DiseaseSurveillanceService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.home')]
class Homepage extends Component
{
    protected DiseaseSurveillanceService $surveillanceService;

    public function boot(DiseaseSurveillanceService $surveillanceService): void
    {
        $this->surveillanceService = $surveillanceService;
    }

    #[Computed]
    public function heatmapData(): array
    {
        return $this->surveillanceService->getHeatmapData(null, '30days');
    }

    #[Computed]
    public function latestAnnouncement()
    {
        return Announcement::where('is_active', true)
            ->latest()
            ->first();
    }

    public function render()
    {
        return view('livewire.home.homepage');
    }
}
