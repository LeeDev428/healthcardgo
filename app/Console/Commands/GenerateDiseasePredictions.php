<?php

namespace App\Console\Commands;

use App\Services\SarimaPredictionService;
use Illuminate\Console\Command;

class GenerateDiseasePredictions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'predictions:generate {--months=6 : Number of months to predict ahead}';

    /**
     * The console command description.
     */
    protected $description = 'Generate and store disease predictions for all supported disease types';

    public function handle(SarimaPredictionService $service): int
    {
        $months = (int) $this->option('months');

        $this->info("Generating predictions for next {$months} month(s)...");

        try {
            $results = $service->generateAllPredictions($months);

            foreach ($results as $type => $result) {
                if (($result['success'] ?? false) === true) {
                    $this->line(" - {$type}: OK");
                } else {
                    $message = $result['message'] ?? 'Unknown error';
                    $this->warn(" - {$type}: FAILED ({$message})");
                }
            }

            $this->info('Disease predictions generated successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to generate predictions: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
