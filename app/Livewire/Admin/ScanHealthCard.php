<?php

namespace App\Livewire\Admin;

use App\Models\Patient;
use App\Services\HealthCardService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Scan Health Card')]
class ScanHealthCard extends Component
{
    public $scannedData = '';

    public $patientData = null;

    public $error = null;

    public $manualPatientNumber = '';

    public $showManualEntry = false;

    protected $rules = [
        'scannedData' => 'required|string',
        'manualPatientNumber' => 'required|string',
    ];

    public function scanQrCode()
    {
        $this->validate(['scannedData' => 'required|string']);

        try {
            $healthCardService = app(HealthCardService::class);

            // Decrypt the QR code data
            $decryptedData = $healthCardService->decryptQrData($this->scannedData);

            // Find the patient by patient number
            $patient = Patient::where('patient_number', $decryptedData['patient_number'])->first();

            if (! $patient) {
                $this->error = 'Patient not found in the system.';
                $this->patientData = null;

                return;
            }

            // Load relationships
            $patient->load(['user', 'barangay']);

            $this->patientData = [
                'patient' => $patient,
                'qr_data' => $decryptedData,
                'is_valid' => $this->validateQrData($patient, $decryptedData),
            ];

            $this->error = null;
            $this->scannedData = '';

            session()->flash('success', 'Health card scanned successfully!');
        } catch (\Exception $e) {
            $this->error = 'Invalid QR code or decryption failed: '.$e->getMessage();
            $this->patientData = null;
        }
    }

    public function lookupManual()
    {
        $this->validate(['manualPatientNumber' => 'required|string']);

        $patient = Patient::where('patient_number', $this->manualPatientNumber)
            ->with(['user', 'barangay'])
            ->first();

        if (! $patient) {
            $this->error = 'Patient not found with patient number: '.$this->manualPatientNumber;
            $this->patientData = null;

            return;
        }

        $this->patientData = [
            'patient' => $patient,
            'qr_data' => null,
            'is_valid' => true,
        ];

        $this->error = null;
        $this->manualPatientNumber = '';

        session()->flash('success', 'Patient found!');
    }

    public function clearData()
    {
        $this->reset(['scannedData', 'patientData', 'error', 'manualPatientNumber']);
    }

    public function toggleManualEntry()
    {
        $this->showManualEntry = ! $this->showManualEntry;
        $this->clearData();
    }

    private function validateQrData(Patient $patient, array $qrData): bool
    {
        // Validate that the QR data matches the patient record
        if ($patient->patient_number !== $qrData['patient_number']) {
            return false;
        }

        if ($patient->full_name !== $qrData['name']) {
            return false;
        }

        $dateOfBirth = $patient->date_of_birth instanceof \Carbon\Carbon
            ? $patient->date_of_birth
            : \Carbon\Carbon::parse($patient->date_of_birth);

        if ($dateOfBirth->format('Y-m-d') !== $qrData['date_of_birth']) {
            return false;
        }

        // Check if QR code is not too old (optional - e.g., older than 1 year)
        if (isset($qrData['generated_at'])) {
            $generatedAt = \Carbon\Carbon::parse($qrData['generated_at']);
            if ($generatedAt->diffInMonths(now()) > 12) {
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        return view('livewire.admin.scan-health-card');
    }
}
