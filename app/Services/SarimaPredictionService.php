<?php

namespace App\Services;

use App\Models\Disease;
use App\Models\DiseasePrediction;
use App\Models\HistoricalDiseaseData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SarimaPredictionService
{
    /**
     * Generate SARIMA predictions for a disease type
     */
    public function generatePredictions(string $diseaseType, ?int $barangayId = null, int $monthsAhead = 6): array
    {
        // Get historical data (both manual entries and system-generated)
        $historicalData = $this->getHistoricalTimeSeries($diseaseType, $barangayId);

        if (count($historicalData) < 24) {
            return [
                'success' => false,
                'message' => 'Insufficient historical data. Need at least 24 months of data for accurate predictions.',
                'data_points' => count($historicalData),
            ];
        }

        // Calculate predictions using simplified SARIMA approach
        $predictions = $this->calculateSarimaPredictions($historicalData, $monthsAhead);

        // Store predictions in database
        $this->storePredictions($diseaseType, $barangayId, $predictions);

        return [
            'success' => true,
            'message' => "Generated {$monthsAhead} months of predictions",
            'predictions' => $predictions,
        ];
    }

    /**
     * Get historical time series data
     */
    protected function getHistoricalTimeSeries(string $diseaseType, ?int $barangayId = null): array
    {
        // Get manual historical data
        $manualData = HistoricalDiseaseData::byDiseaseType($diseaseType)
            ->when($barangayId, fn ($q) => $q->byBarangay($barangayId))
            ->when(! $barangayId, fn ($q) => $q->whereNull('barangay_id'))
            ->orderBy('record_date')
            ->get();

        // Get system-generated data from actual disease records
        $startDate = $manualData->last()?->record_date ?? Carbon::now()->subYears(2);
        $systemData = Disease::confirmed()
            ->byDiseaseType($diseaseType)
            ->when($barangayId, fn ($q) => $q->byBarangay($barangayId))
            ->where('diagnosis_date', '>=', $startDate)
            ->get()
            ->groupBy(function ($disease) {
                return $disease->diagnosis_date->format('Y-m');
            })
            ->map(function ($group) {
                return (object) [
                    'period' => $group->first()->diagnosis_date->format('Y-m'),
                    'case_count' => $group->count(),
                ];
            });

        // Merge both datasets
        $timeSeries = [];

        foreach ($manualData as $data) {
            $period = $data->record_date->format('Y-m');
            $timeSeries[$period] = $data->case_count;
        }

        foreach ($systemData as $data) {
            $period = Carbon::parse($data->period)->format('Y-m');
            if (! isset($timeSeries[$period])) {
                $timeSeries[$period] = $data->case_count;
            }
        }

        ksort($timeSeries);

        return $timeSeries;
    }

    /**
     * Calculate SARIMA predictions using simplified approach
     * This is a simplified implementation. For production, use proper SARIMA libraries.
     */
    protected function calculateSarimaPredictions(array $historicalData, int $monthsAhead): array
    {
        $values = array_values($historicalData);
        $n = count($values);

        // Calculate basic statistics
        $mean = array_sum($values) / $n;
        $trend = $this->calculateTrend($values);
        $seasonality = $this->calculateSeasonality($values, 12); // 12-month seasonality

        $predictions = [];
        $startDate = Carbon::now()->startOfMonth();

        for ($i = 1; $i <= $monthsAhead; $i++) {
            $predictionDate = $startDate->copy()->addMonths($i);

            // Simple forecast: base value + trend + seasonal component
            $baseValue = $mean + ($trend * ($n + $i));
            $seasonalIndex = ($n + $i - 1) % 12;
            $seasonalComponent = $seasonality[$seasonalIndex] ?? 0;

            $predictedValue = max(0, $baseValue + $seasonalComponent);

            // Calculate confidence intervals (simplified)
            $stdDev = $this->calculateStdDev($values);
            $confidenceMultiplier = 1.96; // 95% confidence interval

            $predictions[] = [
                'prediction_date' => $predictionDate,
                'predicted_cases' => round($predictedValue, 2),
                'confidence_interval_lower' => max(0, round($predictedValue - ($confidenceMultiplier * $stdDev), 2)),
                'confidence_interval_upper' => round($predictedValue + ($confidenceMultiplier * $stdDev), 2),
            ];
        }

        return $predictions;
    }

    /**
     * Calculate linear trend
     */
    protected function calculateTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0;
        }

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($values as $i => $y) {
            $x = $i + 1;
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);

        return $slope;
    }

    /**
     * Calculate seasonal components
     */
    protected function calculateSeasonality(array $values, int $period): array
    {
        $seasonality = array_fill(0, $period, 0);
        $counts = array_fill(0, $period, 0);

        $mean = array_sum($values) / count($values);

        foreach ($values as $i => $value) {
            $seasonIndex = $i % $period;
            $seasonality[$seasonIndex] += $value - $mean;
            $counts[$seasonIndex]++;
        }

        // Average seasonal deviations
        for ($i = 0; $i < $period; $i++) {
            if ($counts[$i] > 0) {
                $seasonality[$i] /= $counts[$i];
            }
        }

        return $seasonality;
    }

    /**
     * Calculate standard deviation
     */
    protected function calculateStdDev(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 1;
        }

        $mean = array_sum($values) / $n;
        $sumSquares = 0;

        foreach ($values as $value) {
            $sumSquares += pow($value - $mean, 2);
        }

        return sqrt($sumSquares / ($n - 1));
    }

    /**
     * Store predictions in database
     */
    protected function storePredictions(string $diseaseType, ?int $barangayId, array $predictions): void
    {
        foreach ($predictions as $prediction) {
            DiseasePrediction::updateOrCreate(
                [
                    'disease_type' => $diseaseType,
                    'barangay_id' => $barangayId,
                    'prediction_date' => $prediction['prediction_date'],
                ],
                [
                    'predicted_cases' => $prediction['predicted_cases'],
                    'confidence_interval_lower' => $prediction['confidence_interval_lower'],
                    'confidence_interval_upper' => $prediction['confidence_interval_upper'],
                    'model_version' => 'v1.0-sarima-simplified',
                    'accuracy_metrics' => [
                        'method' => 'simplified_sarima',
                        'generated_at' => now()->toDateTimeString(),
                    ],
                ]
            );
        }
    }

    /**
     * Check if sufficient data exists for predictions
     */
    public function hasSufficientData(string $diseaseType, ?int $barangayId = null): array
    {
        $historicalData = $this->getHistoricalTimeSeries($diseaseType, $barangayId);
        $dataPoints = count($historicalData);

        return [
            'sufficient' => $dataPoints >= 24,
            'data_points' => $dataPoints,
            'required' => 24,
            'missing' => max(0, 24 - $dataPoints),
        ];
    }

    /**
     * Generate predictions for all disease types
     */
    public function generateAllPredictions(int $monthsAhead = 6): array
    {
        $diseaseTypes = ['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications'];
        $results = [];

        foreach ($diseaseTypes as $diseaseType) {
            try {
                $result = $this->generatePredictions($diseaseType, null, $monthsAhead);
                $results[$diseaseType] = $result;
            } catch (\Exception $e) {
                Log::error("Failed to generate predictions for {$diseaseType}: ".$e->getMessage());
                $results[$diseaseType] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
