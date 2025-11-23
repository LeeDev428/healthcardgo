<?php

namespace App\Livewire\Patient;

use App\Models\Barangay;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.patient')]
#[Title('My Profile')]
class Profile extends Component
{
    use WithFileUploads;

    // Patient fields
    #[Validate('required|date|before:today')]
    public $date_of_birth = '';

    #[Validate('required|in:male,female')]
    public $gender = '';

    #[Validate('required|exists:barangays,id')]
    public $barangay_id = '';

    #[Validate('nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-')]
    public $blood_type = '';

    #[Validate('nullable|string')]
    public $philhealth_number = '';

    #[Validate('required|string|max:255')]
    public $emergency_contact_name = '';

    #[Validate('required|string')]
    public $emergency_contact_number = '';

    #[Validate('nullable|string')]
    public $emergency_contact_relationship = '';

    #[Validate('nullable|string')]
    public $allergies = '';

    #[Validate('nullable|string')]
    public $current_medications = '';

    #[Validate('nullable|string')]
    public $medical_history = '';

    #[Validate('nullable|string')]
    public $accessibility_requirements = '';

    #[Validate('nullable|image|max:2048')]
    public $photo;

    public $existing_photo_path = null;

    public $patient = null;

    public function mount(): void
    {
        $this->patient = Auth::user()->patient;

        if ($this->patient) {
            $this->date_of_birth = $this->patient->date_of_birth?->format('Y-m-d') ?? '';
            $this->gender = $this->patient->gender ?? '';
            $this->barangay_id = $this->patient->barangay_id ?? '';
            $this->blood_type = $this->patient->blood_type ?? '';
            $this->philhealth_number = $this->patient->philhealth_number ?? '';
            $this->accessibility_requirements = $this->patient->accessibility_requirements ?? '';
            $this->existing_photo_path = $this->patient->photo_path;

            // Emergency contact
            $emergencyContact = $this->patient->emergency_contact ?? [];
            $this->emergency_contact_name = $emergencyContact['name'] ?? '';
            $this->emergency_contact_number = $emergencyContact['number'] ?? $emergencyContact['phone'] ?? '';
            $this->emergency_contact_relationship = $emergencyContact['relationship'] ?? '';

            // Medical information (convert arrays to strings for display)
            $this->allergies = is_array($this->patient->allergies)
                ? implode(', ', array_filter($this->patient->allergies))
                : ($this->patient->allergies ?? '');

            $this->current_medications = is_array($this->patient->current_medications)
                ? implode(', ', array_filter($this->patient->current_medications))
                : ($this->patient->current_medications ?? '');

            $this->medical_history = is_array($this->patient->medical_history)
                ? implode(', ', array_filter($this->patient->medical_history))
                : ($this->patient->medical_history ?? '');
        }
    }

    public function save(): void
    {
        $this->validate();

        // Handle photo upload
        $photoPath = $this->existing_photo_path;
        if ($this->photo) {
            // Delete old photo if exists
            if ($this->existing_photo_path) {
                Storage::disk('public')->delete($this->existing_photo_path);
            }
            $photoPath = $this->photo->store('patient-photos', 'public');
        }

        $patientData = [
            'user_id' => Auth::id(),
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'barangay_id' => $this->barangay_id,
            'blood_type' => $this->blood_type,
            'philhealth_number' => $this->philhealth_number,
            'photo_path' => $photoPath,
            'accessibility_requirements' => $this->accessibility_requirements,
            'emergency_contact' => [
                'name' => $this->emergency_contact_name,
                'number' => $this->emergency_contact_number,
                'relationship' => $this->emergency_contact_relationship,
            ],
            'allergies' => $this->allergies ? array_map('trim', explode(',', $this->allergies)) : null,
            'current_medications' => $this->current_medications ? array_map('trim', explode(',', $this->current_medications)) : null,
            'medical_history' => $this->medical_history ? array_map('trim', explode(',', $this->medical_history)) : null,
        ];

        if ($this->patient) {
            $this->patient->update($patientData);
            session()->flash('success', 'Profile updated successfully!');
        } else {
            Patient::create($patientData);
            session()->flash('success', 'Profile created successfully!');
        }

        $this->dispatch('profile-updated');
        $this->redirect(route('patient.dashboard'), navigate: true);
    }

    public function removePhoto(): void
    {
        if ($this->existing_photo_path) {
            Storage::disk('public')->delete($this->existing_photo_path);
            $this->patient->update(['photo_path' => null]);
            $this->existing_photo_path = null;
            session()->flash('success', 'Photo removed successfully!');
        }
    }

    public function render()
    {
        return view('livewire.patient.profile', [
            'barangays' => Barangay::orderBy('name')->get(),
        ]);
    }
}
