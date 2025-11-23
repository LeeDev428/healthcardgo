<?php

namespace App\Services\MedicalRecordTemplates;

class HIVTestingTemplate implements MedicalRecordTemplateInterface
{
    public static function getFields(): array
    {
        return [
            'pre_test_counseling' => [
                'label' => 'Pre-Test Counseling Notes',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Counseling provided, patient understanding, consent obtained',
            ],
            'risk_assessment' => [
                'label' => 'Risk Assessment',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Risk factors, exposure history, behavioral assessment',
            ],
            'test_type' => [
                'label' => 'Test Type',
                'type' => 'select',
                'options' => ['Rapid Test', 'ELISA', 'Western Blot', 'Viral Load'],
                'required' => true,
            ],
            'test_date' => [
                'label' => 'Test Date',
                'type' => 'date',
                'required' => true,
            ],
            'test_result' => [
                'label' => 'Test Result',
                'type' => 'select',
                'options' => ['Reactive', 'Non-Reactive', 'Indeterminate', 'Pending'],
                'required' => true,
            ],
            'cd4_count' => [
                'label' => 'CD4 Count (if applicable)',
                'type' => 'number',
                'required' => false,
                'placeholder' => 'cells/μL',
            ],
            'viral_load' => [
                'label' => 'Viral Load (if applicable)',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'copies/mL',
            ],
            'post_test_counseling' => [
                'label' => 'Post-Test Counseling Notes',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Result disclosure, emotional support, next steps discussed',
            ],
            'treatment_initiation' => [
                'label' => 'Treatment Initiation (if positive)',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'ART regimen, baseline assessments, referrals',
            ],
            'follow_up_schedule' => [
                'label' => 'Follow-up Schedule',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Next appointment dates, monitoring plan',
            ],
            'confidentiality_note' => [
                'label' => 'Confidentiality Reminder',
                'type' => 'checkbox',
                'required' => true,
                'default' => true,
                'description' => 'Patient informed about confidentiality and data protection',
            ],
            'referrals' => [
                'label' => 'Referrals Made',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Referrals to specialists, support groups, social services',
            ],
            'doctor_notes' => [
                'label' => 'Additional Clinical Notes (Encrypted)',
                'type' => 'textarea',
                'required' => false,
                'encrypted' => true,
            ],
        ];
    }

    public static function getTemplateName(): string
    {
        return 'HIV Testing/Consultation';
    }

    public static function getCategory(): string
    {
        return 'hiv';
    }
}
