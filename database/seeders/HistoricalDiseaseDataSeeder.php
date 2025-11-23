<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\HistoricalDiseaseData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HistoricalDiseaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role_id', 1)->first();

        if (! $admin) {
            $this->command->warn('No admin user found. Please run user seeder first.');

            return;
        }

        $diseaseTypes = ['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications'];
        $barangays = Barangay::limit(10)->get(); // Get first 10 barangays

        // Generate 36 months of historical data (3 years)
        $startDate = Carbon::now()->subMonths(36)->startOfMonth();

        foreach ($diseaseTypes as $diseaseType) {
            // City-wide data
            for ($i = 0; $i < 36; $i++) {
                $date = $startDate->copy()->addMonths($i);

                // Generate realistic case counts with seasonal patterns
                $baseCount = $this->getBaseCount($diseaseType);
                $seasonalFactor = $this->getSeasonalFactor($diseaseType, $date->month);
                $randomVariation = rand(-3, 3);
                $caseCount = max(0, $baseCount + $seasonalFactor + $randomVariation);

                HistoricalDiseaseData::create([
                    'disease_type' => $diseaseType,
                    'barangay_id' => null, // City-wide
                    'record_date' => $date,
                    'case_count' => $caseCount,
                    'notes' => 'Historical data for '.$date->format('F Y'),
                    'data_source' => 'manual',
                    'created_by' => $admin->id,
                ]);
            }

            // Barangay-specific data for some barangays
            foreach ($barangays->take(5) as $barangay) {
                for ($i = 0; $i < 36; $i++) {
                    $date = $startDate->copy()->addMonths($i);

                    $baseCount = $this->getBaseCount($diseaseType) / 10; // Lower for individual barangays
                    $seasonalFactor = $this->getSeasonalFactor($diseaseType, $date->month) / 2;
                    $randomVariation = rand(-1, 2);
                    $caseCount = max(0, round($baseCount + $seasonalFactor + $randomVariation));

                    HistoricalDiseaseData::create([
                        'disease_type' => $diseaseType,
                        'barangay_id' => $barangay->id,
                        'record_date' => $date,
                        'case_count' => $caseCount,
                        'notes' => "Historical data for {$barangay->name}",
                        'data_source' => 'manual',
                        'created_by' => $admin->id,
                    ]);
                }
            }
        }

        $this->command->info('Historical disease data seeded successfully!');
    }

    /**
     * Get base case count for disease type
     */
    private function getBaseCount(string $diseaseType): int
    {
        return match ($diseaseType) {
            'dengue' => 25,
            'measles' => 8,
            'hiv' => 3,
            'malaria' => 2,
            'rabies' => 4,
            'pregnancy_complications' => 15,
            default => 5,
        };
    }

    /**
     * Get seasonal variation factor
     */
    private function getSeasonalFactor(string $diseaseType, int $month): int
    {
        // Dengue peaks during rainy season (June-November)
        if ($diseaseType === 'dengue') {
            return in_array($month, [6, 7, 8, 9, 10, 11]) ? 10 : -5;
        }

        // Measles peaks during dry season (March-May)
        if ($diseaseType === 'measles') {
            return in_array($month, [3, 4, 5]) ? 5 : -2;
        }

        // Pregnancy complications slightly higher in certain months
        if ($diseaseType === 'pregnancy_complications') {
            return in_array($month, [1, 2, 12]) ? 3 : 0;
        }

        return 0;
    }
}
