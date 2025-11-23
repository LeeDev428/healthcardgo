<?php

namespace App\Livewire\HealthcareAdmin;

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Patient;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PatientRegistrationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegisterPatient extends Component
{
    use WithFileUploads;

    // Registration mode
    public bool $create_user_account = false;

    // User fields
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $contact_number = '';

    // Patient fields
    public string $date_of_birth = '';

    public string $gender = '';

    public string $barangay_id = '';

    public string $blood_type = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_number = '';

    public string $allergies = '';

    public string $current_medications = '';

    public string $medical_history = '';

    public $photo; // temporary uploaded file

    // Disease capture (for walk-ins)
    public string $disease_type = '';

    public function mount(): void
    {
        // Ensure only medical records admin can access
        $user = Auth::user();
        if (! $user || $user->role?->name !== 'healthcare_admin' || $user->admin_category?->value !== 'medical_records') {
            abort(403, 'Unauthorized');
        }
    }

    public function rules(): array
    {
        $base = [
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'barangay_id' => 'required|exists:barangays,id',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ];

        if ($this->create_user_account) {
            $base['email'] = 'required|email|unique:users,email';
            $base['password'] = 'required|min:8|confirmed';
            $base['contact_number'] = 'required|string|unique:users,contact_number';
        } else {
            // Walk-in disease type is required when not creating a user
            $base['disease_type'] = 'required|in:rabies,malaria,dengue,measles';
        }

        return $base;
    }

    public function submit(): void
    {
        $this->validate();

        try {
            if ($this->create_user_account) {
                /** @var PatientRegistrationService $registration */
                $registration = app(PatientRegistrationService::class);
                $user = $registration->register([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'contact_number' => $this->contact_number,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                    'barangay_id' => $this->barangay_id,
                    'blood_type' => $this->blood_type ?: null,
                    'emergency_contact_name' => $this->emergency_contact_name,
                    'emergency_contact_number' => $this->emergency_contact_number,
                    'allergies' => $this->allergies,
                    'current_medications' => $this->current_medications,
                    'medical_history' => $this->medical_history,
                    'photo' => $this->photo,
                ], Auth::user(), internal: true);

                // Send notifications
                /** @var NotificationService $notifier */
                $notifier = app(NotificationService::class);
                $notifier->sendPatientCreatedByAdmin($user, Auth::user());

                // Also record disease type if provided
                if (! empty($this->disease_type)) {
                    $patient = $user->patient;
                    if ($patient) {
                        $this->createDiseaseRecord($patient->id);
                    }
                }
            } else {
                // Walk-in: create patient without user
                $photoPath = null;
                if (! empty($this->photo)) {
                    $photoPath = $this->photo->store('patient-photos', 'public');
                }

                $patient = Patient::create([
                    'user_id' => null,
                    'full_name' => $this->name,
                    'contact_number' => $this->contact_number,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                    'barangay_id' => $this->barangay_id,
                    'blood_type' => $this->blood_type ?: null,
                    'photo_path' => $photoPath,
                    'emergency_contact' => [
                        'name' => $this->emergency_contact_name,
                        'number' => $this->emergency_contact_number,
                    ],
                    'allergies' => ! empty($this->allergies) ? [$this->allergies] : null,
                    'current_medications' => ! empty($this->current_medications) ? [$this->current_medications] : null,
                    'medical_history' => ! empty($this->medical_history) ? [$this->medical_history] : null,
                ]);

                // Create required disease record for walk-in
                $this->createDiseaseRecord($patient->id);
            }

            $this->reset([
                'create_user_account', 'name', 'email', 'password', 'password_confirmation', 'contact_number', 'date_of_birth', 'gender', 'barangay_id', 'blood_type', 'emergency_contact_name', 'emergency_contact_number', 'allergies', 'current_medications', 'medical_history', 'photo', 'disease_type',
            ]);

            $this->dispatch('success', message: 'Patient registered successfully.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to register patient: '.$e->getMessage());
        }
    }

    protected function createDiseaseRecord(int $patientId): void
    {
        // Generate a simple case number: DIS-YYYYMMDD-XXXX
        $prefix = 'DIS-'.now()->format('Ymd').'-';
        $sequence = str_pad((string) (Disease::whereDate('reported_date', now()->toDateString())->count() + 1), 4, '0', STR_PAD_LEFT);
        $caseNumber = $prefix.$sequence;

        Disease::create([
            'patient_id' => $patientId,
            'medical_record_id' => null,
            'disease_type' => $this->disease_type,
            'case_number' => $caseNumber,
            'status' => 'suspected',
            'onset_date' => null,
            'reported_date' => now()->toDateString(),
            'confirmed_date' => null,
            'diagnosis_date' => now()->toDateString(),
            'barangay_id' => $this->barangay_id ?: null,
            'symptoms' => null,
            'risk_factors' => null,
            'treatment_notes' => null,
            'severity' => null,
            'reported_by' => Auth::id(),
        ]);
    }

    public function render()
    {
        return view('livewire.healthcare-admin.register-patient', [
            'barangays' => Barangay::orderBy('name')->get(),
        ]);
    }
}
