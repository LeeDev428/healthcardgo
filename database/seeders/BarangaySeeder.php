<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            ['name' => 'Manay', 'latitude' => 7.3450, 'longitude' => 125.6010],
            ['name' => 'Nanyo', 'latitude' => 7.3320, 'longitude' => 125.6370],
            ['name' => 'Barangay Southern Davao', 'latitude' => 7.3320, 'longitude' => 125.6570],
            ['name' => 'Quezon', 'latitude' => 7.3290, 'longitude' => 125.6802],
            ['name' => 'Gredu (Poblacion)', 'latitude' => 7.2962, 'longitude' => 125.6795],
            ['name' => 'New Visayas', 'latitude' => 7.3080, 'longitude' => 125.6680],
            ['name' => 'San Pedro', 'latitude' => 7.2970, 'longitude' => 125.7094],
            ['name' => 'San Francisco', 'latitude' => 7.3068, 'longitude' => 125.6803],
            ['name' => 'Santo Niño', 'latitude' => 7.3055, 'longitude' => 125.6897],
            ['name' => 'Katipunan', 'latitude' => 7.2995, 'longitude' => 125.6310],
            ['name' => 'Tagpore', 'latitude' => 7.2730, 'longitude' => 125.6250],
            ['name' => 'DAPCO', 'latitude' => 7.3921, 'longitude' => 125.5983],
            ['name' => 'Cagangohan', 'latitude' => 7.2861871, 'longitude' => 125.68180],
            ['name' => 'Little Panay', 'latitude' => 7.2948, 'longitude' => 125.6480],
            ['name' => 'Tibungol', 'latitude' => 7.4005, 'longitude' => 125.5485],
            ['name' => 'Salvacion', 'latitude' => 7.31310, 'longitude' => 125.6855],
            ['name' => 'Consolacion', 'latitude' => 7.3155, 'longitude' => 125.5535],
            ['name' => 'J.P. Laurel', 'latitude' => 7.2745, 'longitude' => 125.6695],
            ['name' => 'San Roque', 'latitude' => 7.2914, 'longitude' => 125.6897],
            ['name' => 'San Isidro', 'latitude' => 7.2920, 'longitude' => 125.6733],
            ['name' => 'San Nicolas', 'latitude' => 7.2626, 'longitude' => 125.6181],
            ['name' => 'Waterfall', 'latitude' => 7.2886, 'longitude' => 125.5834],
            ['name' => 'Upper Licanan', 'latitude' => 7.2856, 'longitude' => 125.6325],
            ['name' => 'Malativas', 'latitude' => 7.2936, 'longitude' => 125.5648],
            ['name' => 'Maduao', 'latitude' => 7.2796, 'longitude' => 125.6433],
            ['name' => 'Mabunao', 'latitude' => 7.2660, 'longitude' => 125.6630],
            ['name' => 'New Pandan', 'latitude' => 7.2973, 'longitude' => 125.6801],
            ['name' => 'New Malitbog', 'latitude' => 7.3339, 'longitude' => 125.6209],
            ['name' => 'A. O. Floirendo', 'latitude' => 7.3977, 'longitude' => 125.5802],
            ['name' => 'Buenavista', 'latitude' => 7.2756, 'longitude' => 125.5907],
            ['name' => 'Cacao', 'latitude' => 7.3083, 'longitude' => 125.6077],
            ['name' => 'Datu Abdul Dadia', 'latitude' => 7.3153, 'longitude' => 125.6548],
            ['name' => 'Kasilak', 'latitude' => 7.3268, 'longitude' => 125.5951],
            ['name' => 'Katualan', 'latitude' => 7.2301, 'longitude' => 125.5543],
            ['name' => 'Kauswagan', 'latitude' => 7.3102, 'longitude' => 125.5831],
            ['name' => 'Kiotoy', 'latitude' => 7.2443, 'longitude' => 125.6077],
            ['name' => 'Lower Panaga', 'latitude' => 7.4320, 'longitude' => 125.5640],
            ['name' => 'New Malaga', 'latitude' => 7.3442, 'longitude' => 125.5725],
            ['name' => 'San Vicente', 'latitude' => 7.3088, 'longitude' => 125.7003],
            ['name' => 'Santa Cruz', 'latitude' => 7.2365, 'longitude' => 125.5896],
            ['name' => 'Sindaton', 'latitude' => 7.4396, 'longitude' => 125.5842],
            ['name' => 'Matignao', 'latitude' => 7.2850, 'longitude' => 125.6100],
        ];

        DB::table('barangays')->insert($barangays);

        foreach ($barangays as $barangay) {
            // Update created_at and updated_at timestamps
            DB::table('barangays')
                ->where('name', $barangay['name'])
                ->update([
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $this->command->info('Barangays seeded successfully.');
    }
}
