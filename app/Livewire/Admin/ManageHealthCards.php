<?php

namespace App\Livewire\Admin;

use App\Models\HealthCard;
use App\Models\Patient;
use App\Services\HealthCardPredictionService;
use App\Services\HealthCardService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Health Cards')]
class ManageHealthCards extends Component
{
    use WithPagination;

    public $search = '';

    public $showCreateModal = false;

    public $showViewModal = false;

    public $selectedCard = null;

    public $selectedPatient = null;

    // Form fields
    public $patient_id;

    public $issue_date;

    public $expiry_date;

    public $status = 'active';

    public $notes = '';

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'issue_date' => 'required|date',
        'expiry_date' => 'required|date|after:issue_date',
        'status' => 'required|in:active,expired,suspended,revoked',
        'notes' => 'nullable|string',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->issue_date = now()->format('Y-m-d');
        $this->expiry_date = now()->addYear()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function generateCard()
    {
        $this->validate();

        $patient = Patient::findOrFail($this->patient_id);
        $healthCardService = app(HealthCardService::class);

        // Validate patient has all required data
        if (! $healthCardService->hasValidHealthCard($patient)) {
            session()->flash('error', 'Patient profile is incomplete. Required: Patient Number, Blood Type, Date of Birth, Barangay, and Photo.');

            return;
        }

        try {
            // Generate QR code with encrypted data
            $qrCodeDataUri = $healthCardService->generateQrCode($patient);

            // Save PDF and PNG to storage
            $pdfPath = $healthCardService->saveHealthCardPdf($patient);
            $pngPath = $healthCardService->generateHealthCardPng($patient);

            // Create health card record
            $card = HealthCard::create([
                'patient_id' => $this->patient_id,
                'card_number' => HealthCard::generateCardNumber(),
                'issue_date' => $this->issue_date,
                'expiry_date' => $this->expiry_date,
                'qr_code' => $qrCodeDataUri,
                'status' => $this->status,
                'medical_data' => [
                    'blood_type' => $patient->blood_type,
                    'allergies' => $patient->allergies,
                    'emergency_contact' => $patient->emergency_contact,
                    'barangay' => $patient->barangay?->name,
                    'pdf_path' => $pdfPath,
                    'png_path' => $pngPath,
                    'notes' => $this->notes,
                ],
            ]);

            $this->showCreateModal = false;
            $this->resetForm();
            session()->flash('success', 'Health card generated successfully! Card Number: '.$card->card_number);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate health card: '.$e->getMessage());
        }
    }

    public function viewCard(HealthCard $card)
    {
        $this->selectedCard = $card;
        $this->selectedPatient = $card->patient;
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedCard = null;
        $this->selectedPatient = null;
    }

    public function downloadPdf(HealthCard $card)
    {
        $pdfPath = $card->medical_data['pdf_path'] ?? null;

        if (! $pdfPath || ! Storage::disk('public')->exists($pdfPath)) {
            session()->flash('error', 'PDF file not found. Please regenerate the health card.');

            return;
        }

        return response()->download(
            Storage::disk('public')->path($pdfPath),
            'healthcard_'.$card->card_number.'.pdf'
        );
    }

    public function downloadPng(HealthCard $card)
    {
        $pngPath = $card->medical_data['png_path'] ?? null;

        if (! $pngPath || ! Storage::disk('public')->exists($pngPath)) {
            session()->flash('error', 'PNG file not found. Please regenerate the health card.');

            return;
        }

        return response()->download(
            Storage::disk('public')->path($pngPath),
            'healthcard_'.$card->card_number.'.png'
        );
    }

    public function regeneratePdf(HealthCard $card)
    {
        try {
            $healthCardService = app(HealthCardService::class);
            $pdfPath = $healthCardService->saveHealthCardPdf($card->patient);
            $pngPath = $healthCardService->generateHealthCardPng($card->patient);

            // Update QR code and file paths
            $qrCodeDataUri = $healthCardService->generateQrCode($card->patient);

            $medicalData = $card->medical_data ?? [];
            $medicalData['pdf_path'] = $pdfPath;
            $medicalData['png_path'] = $pngPath;
            $medicalData['regenerated_at'] = now()->toIso8601String();

            $card->update([
                'qr_code' => $qrCodeDataUri,
                'medical_data' => $medicalData,
            ]);

            session()->flash('success', 'Health card files regenerated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to regenerate files: '.$e->getMessage());
        }
    }

    public function renewCard(HealthCard $card)
    {
        $newExpiry = now()->addYear();

        $card->update([
            'expiry_date' => $newExpiry,
            'status' => 'active',
            'last_renewed_at' => now(),
        ]);

        // Regenerate PDF with new expiry date
        $this->regeneratePdf($card);

        session()->flash('success', 'Health card renewed successfully! New expiry: '.$newExpiry->format('M d, Y'));
    }

    public function suspendCard(HealthCard $card)
    {
        $card->update(['status' => 'suspended']);
        session()->flash('success', 'Health card suspended successfully!');
    }

    public function revokeCard(HealthCard $card)
    {
        $card->update(['status' => 'revoked']);
        session()->flash('success', 'Health card revoked successfully!');
    }

    public function activateCard(HealthCard $card)
    {
        if ($card->isExpired()) {
            session()->flash('error', 'Cannot activate an expired card. Please renew it first.');

            return;
        }

        $card->update(['status' => 'active']);
        session()->flash('success', 'Health card activated successfully!');
    }

    private function resetForm()
    {
        $this->selectedCard = null;
        $this->patient_id = null;
        $this->issue_date = '';
        $this->expiry_date = '';
        $this->status = 'active';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render()
    {
        $healthCards = HealthCard::with(['patient.user', 'patient.barangay'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient.user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('card_number', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(15);

        // Get chart data with predictions
        $predictionService = app(HealthCardPredictionService::class);
        $chartData = $predictionService->getChartData();

        $patients = Patient::with('user')
            ->whereHas('user', fn ($q) => $q->where('status', 'active'))
            ->whereDoesntHave('healthCards', function ($query) {
                $query->whereIn('status', ['active', 'suspended']);
            })
            ->get();

        return view('livewire.admin.manage-health-cards', [
            'healthCards' => $healthCards,
            'availablePatients' => $patients,
            'chartData' => $chartData,
        ]);
    }
}
