<?php

namespace App\Services\MedicalRecordTemplates;

class GeneralConsultationTemplate implements MedicalRecordTemplateInterface
{
    public static function getFields(): array
    {
        return [
            'chief_complaint' => [
                'label' => 'Chief Complaint',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Main reason for visit',
            ],
            'history_of_present_illness' => [
                'label' => 'History of Present Illness',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Description of current condition',
            ],
            'vital_signs' => [
                'label' => 'Vital Signs',
                'type' => 'group',
                'fields' => [
                    'blood_pressure' => ['label' => 'Blood Pressure', 'type' => 'text', 'placeholder' => 'e.g., 120/80'],
                    'temperature' => ['label' => 'Temperature (°C)', 'type' => 'number', 'step' => '0.1'],
                    'pulse_rate' => ['label' => 'Pulse Rate (bpm)', 'type' => 'number'],
                    'respiratory_rate' => ['label' => 'Respiratory Rate (per min)', 'type' => 'number'],
                    'weight' => ['label' => 'Weight (kg)', 'type' => 'number', 'step' => '0.1'],
                    'height' => ['label' => 'Height (cm)', 'type' => 'number', 'step' => '0.1'],
                ],
            ],
            'physical_examination' => [
                'label' => 'Physical Examination Findings',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'General appearance, HEENT, cardiovascular, respiratory, etc.',
            ],
            'diagnosis' => [
                'label' => 'Diagnosis/Impression',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Primary and secondary diagnoses',
            ],
            'treatment_plan' => [
                'label' => 'Treatment Plan',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Proposed treatment and interventions',
            ],
            'medications' => [
                'label' => 'Medications Prescribed',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Medication name, dosage, frequency, duration',
            ],
            'follow_up' => [
                'label' => 'Follow-up Recommendations',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'When to return, what to watch for',
            ],
            'doctor_notes' => [
                'label' => 'Additional Doctor\'s Notes',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Any additional observations or instructions',
            ],
        ];
    }

    public static function getTemplateName(): string
    {
        return 'General Consultation';
    }

    public static function getCategory(): string
    {
        return 'general';
    }
}
