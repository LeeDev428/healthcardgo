<?php

namespace App\Services\MedicalRecordTemplates;

class ImmunizationTemplate implements MedicalRecordTemplateInterface
{
    public static function getFields(): array
    {
        return [
            'vaccine_type' => [
                'label' => 'Vaccine Type',
                'type' => 'select',
                'options' => [
                    'BCG',
                    'Hepatitis B',
                    'DPT (Diphtheria, Pertussis, Tetanus)',
                    'OPV (Oral Polio Vaccine)',
                    'MMR (Measles, Mumps, Rubella)',
                    'Influenza',
                    'Pneumococcal',
                    'HPV (Human Papillomavirus)',
                    'COVID-19',
                    'Other',
                ],
                'required' => true,
            ],
            'vaccine_name_other' => [
                'label' => 'Specify Other Vaccine',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'If "Other" selected above',
            ],
            'dose_number' => [
                'label' => 'Dose Number',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'e.g., 1st dose, 2nd dose, Booster',
            ],
            'batch_lot_number' => [
                'label' => 'Batch/Lot Number',
                'type' => 'text',
                'required' => true,
            ],
            'manufacturer' => [
                'label' => 'Manufacturer',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Vaccine manufacturer name',
            ],
            'expiration_date' => [
                'label' => 'Expiration Date',
                'type' => 'date',
                'required' => true,
            ],
            'administration_site' => [
                'label' => 'Administration Site',
                'type' => 'select',
                'options' => [
                    'Left Deltoid',
                    'Right Deltoid',
                    'Left Thigh',
                    'Right Thigh',
                    'Left Buttock',
                    'Right Buttock',
                    'Oral',
                    'Other',
                ],
                'required' => true,
            ],
            'route' => [
                'label' => 'Route of Administration',
                'type' => 'select',
                'options' => ['Intramuscular (IM)', 'Subcutaneous (SC)', 'Intradermal (ID)', 'Oral'],
                'required' => true,
            ],
            'dosage' => [
                'label' => 'Dosage',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'e.g., 0.5 mL',
            ],
            'administered_by' => [
                'label' => 'Administered By',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Healthcare provider name',
            ],
            'adverse_reactions' => [
                'label' => 'Adverse Reactions',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Any immediate or reported adverse reactions',
            ],
            'reaction_severity' => [
                'label' => 'Reaction Severity (if any)',
                'type' => 'select',
                'options' => ['None', 'Mild', 'Moderate', 'Severe'],
                'required' => false,
            ],
            'vital_signs_pre' => [
                'label' => 'Vital Signs (Pre-immunization)',
                'type' => 'group',
                'fields' => [
                    'temperature' => ['label' => 'Temperature (°C)', 'type' => 'number', 'step' => '0.1'],
                ],
            ],
            'next_dose_due' => [
                'label' => 'Next Dose Due Date',
                'type' => 'date',
                'required' => false,
            ],
            'parent_guardian_consent' => [
                'label' => 'Parent/Guardian Consent Obtained',
                'type' => 'checkbox',
                'required' => true,
                'description' => 'For minors',
            ],
            'notes' => [
                'label' => 'Additional Notes',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Any additional observations or instructions',
            ],
        ];
    }

    public static function getTemplateName(): string
    {
        return 'Immunization';
    }

    public static function getCategory(): string
    {
        return 'immunization';
    }
}
