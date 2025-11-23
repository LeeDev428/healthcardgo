<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DiseaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctor = User::where('role_id', 3)->first();
        $patients = Patient::limit(50)->get();
        $barangays = Barangay::all();

        if (! $doctor || $patients->isEmpty() || $barangays->isEmpty()) {
            $this->command->warn('Required data missing. Please seed users, patients, and barangays first.');

            return;
        }

        $diseaseTypes = ['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications'];

        // Generate diseases for the last 3 months
        foreach ($diseaseTypes as $diseaseType) {
            $caseCount = $this->getCaseCount($diseaseType);

            for ($i = 0; $i < $caseCount; $i++) {
                $patient = $patients->random();
                $barangay = $barangays->random();
                $daysAgo = rand(1, 90);
                $diagnosisDate = Carbon::now()->subDays($daysAgo);

                Disease::create([
                    'patient_id' => $patient->id,
                    'disease_type' => $diseaseType,
                    'case_number' => 'CASE-'.now()->year.'-'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                    'status' => 'confirmed',
                    'onset_date' => $diagnosisDate->copy()->subDays(rand(1, 7)),
                    'reported_date' => $diagnosisDate->copy()->subDays(rand(0, 3)),
                    'diagnosis_date' => $diagnosisDate,
                    'confirmed_date' => $diagnosisDate->copy()->addDays(rand(1, 3)),
                    'barangay_id' => $barangay->id,
                    'symptoms' => $this->getSymptoms($diseaseType),
                    'risk_factors' => $this->getRiskFactors($diseaseType),
                    'treatment_notes' => 'Standard protocol for '.$diseaseType.' followed.',
                    'severity' => fake()->randomElement(['mild', 'moderate', 'severe']),
                    'reported_by' => $doctor->id,
                ]);
            }
        }

        $this->command->info('Disease cases seeded successfully!');
    }

    /**
     * Get case count for disease type
     */
    private function getCaseCount(string $diseaseType): int
    {
        return match ($diseaseType) {
            'dengue' => 45,
            'measles' => 15,
            'hiv' => 8,
            'malaria' => 5,
            'rabies' => 10,
            'pregnancy_complications' => 25,
            default => 10,
        };
    }

    /**
     * Get symptoms for disease type
     */
    private function getSymptoms(string $diseaseType): array
    {
        return match ($diseaseType) {
            'dengue' => ['fever', 'headache', 'body_aches', 'rash'],
            'measles' => ['fever', 'cough', 'rash', 'red_eyes'],
            'hiv' => ['fever', 'fatigue', 'weight_loss'],
            'malaria' => ['fever', 'chills', 'headache', 'fatigue'],
            'rabies' => ['fever', 'headache', 'anxiety', 'confusion'],
            'pregnancy_complications' => ['bleeding', 'abdominal_pain', 'high_blood_pressure'],
            default => ['fever', 'fatigue'],
        };
    }

    /**
     * Get risk factors for disease type
     */
    private function getRiskFactors(string $diseaseType): string
    {
        return match ($diseaseType) {
            'dengue' => 'Standing water near residence, recent rainfall',
            'measles' => 'Unvaccinated, exposure to infected individual',
            'hiv' => 'High-risk behavior, exposure to infected fluids',
            'malaria' => 'Travel to endemic area, mosquito exposure',
            'rabies' => 'Animal bite, stray dog/cat encounter',
            'pregnancy_complications' => 'Pre-existing conditions, first pregnancy',
            default => 'Standard risk factors',
        };
    }
}
