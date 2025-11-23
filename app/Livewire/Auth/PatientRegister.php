<?php

namespace App\Livewire\Auth;

use App\Models\Barangay;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PatientRegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.auth')]
class PatientRegister extends Component
{
    use WithFileUploads;

    // User fields
    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $contact_number = '';

    // Patient fields
    public $date_of_birth = '';

    public $gender = '';

    public $barangay_id = '';

    public $blood_type = '';

    public $emergency_contact_name = '';

    public $emergency_contact_number = '';

    public $allergies = '';

    public $current_medications = '';

    public $medical_history = '';

    public $photo; // temporary uploaded file

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'contact_number' => 'required|string|unique:users,contact_number',
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
    }

    public function register()
    {
        $this->validate();

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
            'blood_type' => $this->blood_type,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_number' => $this->emergency_contact_number,
            'allergies' => $this->allergies,
            'current_medications' => $this->current_medications,
            'medical_history' => $this->medical_history,
            'photo' => $this->photo,
        ], creator: null, internal: true);

        /** @var NotificationService $notifier */
        $notifier = app(NotificationService::class);
        $notifier->sendNewRegistrationToAdmin($user);

        session()->flash('success', 'Registration submitted successfully! Please wait for admin approval.');

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.patient-register', [
            'barangays' => Barangay::orderBy('name')->get(),
        ]);
    }
}
