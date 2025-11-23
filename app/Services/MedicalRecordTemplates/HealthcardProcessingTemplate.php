<?php

namespace App\Services\MedicalRecordTemplates;

class HealthcardProcessingTemplate implements MedicalRecordTemplateInterface
{
    public static function getFields(): array
    {
        return [
            'personal_information_verified' => [
                'label' => 'Personal Information Verified',
                'type' => 'checkbox',
                'required' => true,
            ],
            'basic_health_assessment' => [
                'label' => 'Basic Health Assessment',
                'type' => 'group',
                'fields' => [
                    'blood_pressure' => ['label' => 'Blood Pressure', 'type' => 'text', 'placeholder' => 'e.g., 120/80'],
                    'weight' => ['label' => 'Weight (kg)', 'type' => 'number', 'step' => '0.1'],
                    'height' => ['label' => 'Height (cm)', 'type' => 'number', 'step' => '0.1'],
                ],
            ],
            'immunization_history' => [
                'label' => 'Immunization History',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'List previous vaccinations and dates',
            ],
            'existing_conditions' => [
                'label' => 'Existing Medical Conditions',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Any chronic or current medical conditions',
            ],
            'healthcard_type' => [
                'label' => 'Healthcard Type',
                'type' => 'select',
                'options' => ['Basic', 'Standard', 'Comprehensive'],
                'required' => true,
            ],
            'validity_period' => [
                'label' => 'Validity Period',
                'type' => 'select',
                'options' => ['1 Year', '2 Years', '3 Years'],
                'required' => true,
            ],
            'fees_collected' => [
                'label' => 'Fees Collected (PHP)',
                'type' => 'number',
                'step' => '0.01',
                'required' => false,
            ],
            'payment_status' => [
                'label' => 'Payment Status',
                'type' => 'select',
                'options' => ['Paid', 'Waived', 'Pending'],
                'required' => true,
            ],
            'notes' => [
                'label' => 'Additional Notes',
                'type' => 'textarea',
                'required' => false,
            ],
        ];
    }

    public static function getTemplateName(): string
    {
        return 'Healthcard Processing';
    }

    public static function getCategory(): string
    {
        return 'healthcard';
    }
}
