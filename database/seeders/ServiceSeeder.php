<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Health Card Processing',
                'description' => 'Initial health card application and processing with medical examination',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'health_card',
                'requirements' => [
                    'Valid ID',
                    'Barangay Certificate',
                    '2x2 ID Photo',
                    'Birth Certificate',
                ],
                'preparation_instructions' => [
                    'Fast for 8 hours before visit for blood tests',
                    'Bring all required documents',
                    'Arrive 15 minutes early',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Health Card Renewal',
                'description' => 'Annual health card renewal with basic health check',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'health_card',
                'requirements' => [
                    'Current Health Card',
                    'Valid ID',
                ],
                'preparation_instructions' => [
                    'Bring current health card',
                    'Fast for 4 hours if blood test needed',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'HIV Testing & Counseling',
                'description' => 'Confidential HIV testing with pre and post-test counseling',
                'duration_minutes' => 0,
                'fee' => 0.00, // Free service
                'category' => 'hiv_testing',
                'requirements' => [
                    'Valid ID',
                    'Informed consent',
                ],
                'preparation_instructions' => [
                    'Be prepared for counseling session',
                    'Results available in 15-30 minutes',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Prenatal Checkup',
                'description' => 'Comprehensive prenatal care and monitoring for pregnant women',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'pregnancy_care',
                'requirements' => [
                    'Maternity Record Book',
                    'Previous lab results if available',
                ],
                'preparation_instructions' => [
                    'Bring maternity book',
                    'List current medications',
                    'Note any concerns or symptoms',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Child Immunization',
                'description' => 'Routine vaccination for infants and children',
                'duration_minutes' => 0,
                'fee' => 0.00, // Free service
                'category' => 'vaccination',
                'requirements' => [
                    'Child\'s Birth Certificate',
                    'Immunization Record',
                    'Parent/Guardian ID',
                ],
                'preparation_instructions' => [
                    'Bring immunization card',
                    'Child should be healthy',
                    'Prepare comfort items for child',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Adult Vaccination',
                'description' => 'Adult immunization services (Flu, Hepatitis, etc.)',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'vaccination',
                'requirements' => [
                    'Valid ID',
                    'Medical history if available',
                ],
                'preparation_instructions' => [
                    'Inform about allergies',
                    'List current medications',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Basic Laboratory Tests',
                'description' => 'Complete Blood Count, Urinalysis, and other basic lab tests',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'laboratory',
                'requirements' => [
                    'Valid ID',
                    'Doctor\'s request if available',
                ],
                'preparation_instructions' => [
                    'Fast for 8-12 hours for certain tests',
                    'Bring clean urine container',
                    'Stay hydrated',
                ],
                'requires_appointment' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Health Education Seminar',
                'description' => 'Community health education on various topics',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'health_education',
                'requirements' => [
                    'Registration form',
                ],
                'preparation_instructions' => [
                    'Bring notepad and pen',
                    'Come with questions',
                ],
                'requires_appointment' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Consultation',
                'description' => 'Immediate medical attention for urgent health concerns',
                'duration_minutes' => 0,
                'fee' => 0.00,
                'category' => 'emergency',
                'requirements' => [
                    'Valid ID or any identification',
                ],
                'preparation_instructions' => [
                    'Bring list of current medications',
                    'Note onset and symptoms',
                ],
                'requires_appointment' => false,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
