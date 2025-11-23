<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\MedicalRecordService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Medical Record')]
class CreateMedicalRecord extends Component
{
    public $appointment_id;

    public $appointment;

    public $patient;

    public $selectedTemplate = '';

    public $recordData = [];

    public $notes = '';

    public $templates = [];

    public $templateFields = [];

    protected $rules = [
        'selectedTemplate' => 'required|string',
        'recordData' => 'required|array',
        'notes' => 'nullable|string',
    ];

    public function mount($appointmentId = null)
    {
        $this->appointment_id = $appointmentId;

        // Load available templates
        $medicalRecordService = app(MedicalRecordService::class);
        $this->templates = $medicalRecordService->getAvailableTemplates();

        // If appointment ID is provided, load appointment and patient data
        if ($this->appointment_id) {
            $this->appointment = Appointment::with(['patient.user', 'patient.barangay', 'service'])
                ->findOrFail($this->appointment_id);

            $this->patient = $this->appointment->patient;

            // Validate that the logged-in doctor is assigned to this appointment
            if ($this->appointment->doctor_id && $this->appointment->doctor_id !== Auth::id()) {
                abort(403, 'You are not authorized to access this appointment.');
            }
        }
    }

    public function selectTemplate($templateName)
    {
        $this->selectedTemplate = $templateName;

        // Find the template
        $template = collect($this->templates)->firstWhere('name', $templateName);

        if ($template) {
            $this->templateFields = $template['fields'];

            // Initialize record data with empty values
            $this->recordData = [];
            foreach ($this->templateFields as $field) {
                $this->recordData[$field['name']] = $field['default'] ?? '';
            }

            // Auto-populate patient demographics
            if ($this->patient) {
                $this->autopopulatePatientData();
            }
        }
    }

    protected function autopopulatePatientData()
    {
        // Map patient data to record fields
        $patientDataMap = [
            'patient_name' => $this->patient->fullName,
            'patient_number' => $this->patient->patient_number,
            'date_of_birth' => $this->patient->user->date_of_birth?->format('Y-m-d'),
            'age' => $this->patient->age,
            'gender' => $this->patient->user->gender,
            'barangay' => $this->patient->barangay?->name,
            'blood_type' => $this->patient->blood_type,
            'allergies' => is_array($this->patient->allergies) ? implode(', ', $this->patient->allergies) : $this->patient->allergies,
            'current_medications' => is_array($this->patient->current_medications) ? implode(', ', $this->patient->current_medications) : $this->patient->current_medications,
        ];

        foreach ($patientDataMap as $fieldName => $value) {
            if (isset($this->recordData[$fieldName]) && empty($this->recordData[$fieldName])) {
                $this->recordData[$fieldName] = $value;
            }
        }
    }

    public function createRecord()
    {
        $this->validate();

        try {
            $medicalRecordService = app(MedicalRecordService::class);

            // Get template to determine category
            $template = collect($this->templates)->firstWhere('name', $this->selectedTemplate);

            $recordData = [
                'patient_id' => $this->patient->id,
                'appointment_id' => $this->appointment_id,
                'doctor_id' => Auth::id(),
                'category' => $template['category'],
                'template_type' => $this->selectedTemplate,
                'record_data' => $this->recordData,
                'notes' => $this->notes,
            ];

            $medicalRecord = $medicalRecordService->createMedicalRecord($recordData);

            // If appointment exists, mark it as completed
            if ($this->appointment) {
                $this->appointment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            session()->flash('success', 'Medical record created successfully!');

            // Redirect to patient's medical records or appointments list
            return redirect()->route('doctor.appointments.list');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create medical record: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.doctor.create-medical-record');
    }
}
