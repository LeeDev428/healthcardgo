<?php

namespace App\Livewire\Patient;

use App\Services\HealthCardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('My Health Card')]
class ViewHealthCard extends Component
{
    public $patient;

    public $healthCard = null;

    public $hasHealthCard = false;

    public function mount()
    {
        $this->patient = Auth::user()->patient;

        if (! $this->patient) {
            // Patient profile not found - show message but don't abort
            $this->hasHealthCard = false;

            return;
        }

        // Get active health card
        $this->healthCard = $this->patient->healthCards()
            ->whereIn('status', ['active', 'suspended'])
            ->latest()
            ->first();

        $this->hasHealthCard = $this->healthCard !== null;
    }

    public function downloadPdf(HealthCardService $healthCardService)
    {
        if (! $this->healthCard) {
            session()->flash('error', 'No health card found.');

            return;
        }

        try {
            // Check if PDF already exists
            $pdfPath = $this->healthCard->medical_data['pdf_path'] ?? null;

            // If PDF doesn't exist or file is missing, generate new one
            if (! $pdfPath || ! Storage::disk('public')->exists($pdfPath)) {
                $pdfPath = $healthCardService->saveHealthCardPdf($this->patient);

                // Update health card record with PDF path
                $medicalData = $this->healthCard->medical_data;
                $medicalData['pdf_path'] = $pdfPath;
                $this->healthCard->update(['medical_data' => $medicalData]);

                session()->flash('success', 'Health card PDF generated successfully.');
            }

            // Download the PDF
            return response()->download(
                Storage::disk('public')->path($pdfPath),
                'healthcard_'.$this->healthCard->card_number.'.pdf'
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate PDF. Please try again or contact support.');

            return;
        }
    }

    public function downloadPng(HealthCardService $healthCardService)
    {
        if (! $this->healthCard) {
            session()->flash('error', 'No health card found.');

            return;
        }

        try {
            // Check if PNG already exists
            $pngPath = $this->healthCard->medical_data['png_path'] ?? null;

            // If PNG doesn't exist or file is missing, generate new one
            if (! $pngPath || ! Storage::disk('public')->exists($pngPath)) {
                $pngPath = $healthCardService->generateHealthCardPng($this->patient);

                // Update health card record with PNG path
                $medicalData = $this->healthCard->medical_data;
                $medicalData['png_path'] = $pngPath;
                $this->healthCard->update(['medical_data' => $medicalData]);

                session()->flash('success', 'Health card image generated successfully.');
            }

            // Download the PNG
            return response()->download(
                Storage::disk('public')->path($pngPath),
                'healthcard_'.$this->healthCard->card_number.'.png'
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate image. Please try again or contact support.');

            return;
        }
    }

    public function render()
    {
        return view('livewire.patient.view-health-card');
    }
}
