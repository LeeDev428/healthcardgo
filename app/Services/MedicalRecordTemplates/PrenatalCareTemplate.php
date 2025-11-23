<?php

namespace App\Services\MedicalRecordTemplates;

class PrenatalCareTemplate implements MedicalRecordTemplateInterface
{
    public static function getFields(): array
    {
        return [
            'obstetric_history' => [
                'label' => 'Obstetric History (G/P/A)',
                'type' => 'group',
                'fields' => [
                    'gravida' => ['label' => 'Gravida (G)', 'type' => 'number', 'placeholder' => 'Total pregnancies'],
                    'para' => ['label' => 'Para (P)', 'type' => 'number', 'placeholder' => 'Live births'],
                    'abortions' => ['label' => 'Abortions (A)', 'type' => 'number', 'placeholder' => 'Miscarriages/abortions'],
                ],
            ],
            'lmp' => [
                'label' => 'Last Menstrual Period (LMP)',
                'type' => 'date',
                'required' => true,
            ],
            'edd' => [
                'label' => 'Expected Date of Delivery (EDD)',
                'type' => 'date',
                'required' => true,
            ],
            'gestational_age' => [
                'label' => 'Gestational Age',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'e.g., 24 weeks 3 days',
            ],
            'vital_signs' => [
                'label' => 'Vital Signs',
                'type' => 'group',
                'fields' => [
                    'blood_pressure' => ['label' => 'Blood Pressure', 'type' => 'text', 'placeholder' => 'e.g., 120/80'],
                    'weight' => ['label' => 'Weight (kg)', 'type' => 'number', 'step' => '0.1'],
                    'height' => ['label' => 'Height (cm)', 'type' => 'number', 'step' => '0.1'],
                ],
            ],
            'fundic_height' => [
                'label' => 'Fundic Height (cm)',
                'type' => 'number',
                'step' => '0.1',
                'required' => false,
            ],
            'fetal_heart_rate' => [
                'label' => 'Fetal Heart Rate (bpm)',
                'type' => 'number',
                'required' => false,
            ],
            'fetal_position' => [
                'label' => 'Fetal Position/Presentation',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'e.g., Cephalic, Breech',
            ],
            'weight_gain' => [
                'label' => 'Weight Gain This Pregnancy (kg)',
                'type' => 'number',
                'step' => '0.1',
                'required' => false,
            ],
            'laboratory_results' => [
                'label' => 'Laboratory Results',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Blood type, Hb, urinalysis, glucose, etc.',
            ],
            'ultrasound_findings' => [
                'label' => 'Ultrasound Findings',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Fetal measurements, anomalies, placental location',
            ],
            'risk_factors' => [
                'label' => 'Risk Factors Identified',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'High BP, diabetes, previous complications, etc.',
            ],
            'prenatal_vitamins' => [
                'label' => 'Prenatal Vitamins/Supplements',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Iron, folic acid, calcium, etc.',
            ],
            'advice_given' => [
                'label' => 'Health Advice Given',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Nutrition, exercise, danger signs, etc.',
            ],
            'next_visit' => [
                'label' => 'Next Visit Schedule',
                'type' => 'date',
                'required' => true,
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
        return 'Prenatal Care';
    }

    public static function getCategory(): string
    {
        return 'pregnancy';
    }
}
