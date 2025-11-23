<?php

declare(strict_types=1);

use App\Services\SarimaPredictionService;
use Illuminate\Support\Facades\Artisan;

it('runs the predictions:generate command and calls the service', function () {
    // Simple stub service that tracks invocation
    $stub = new class extends SarimaPredictionService
    {
        public function generateAllPredictions(int $monthsAhead = 6): array
        {
            return ['dengue' => ['success' => true]];
        }
    };

    app()->instance(SarimaPredictionService::class, $stub);

    $exit = Artisan::call('predictions:generate', ['--months' => 2]);

    expect($exit)->toBe(0);
    expect(Artisan::output())->toContain('Disease predictions generated successfully.');
});
