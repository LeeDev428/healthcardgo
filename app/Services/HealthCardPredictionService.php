<?php

namespace App\Services;

use App\Models\HealthCard;
use App\Models\HistoricalHealthCardData;
use Carbon\Carbon;

class HealthCardPredictionService
{
    /**
     * Generate SARIMA predictions for health card issuance
     */
    public function generatePredictions(int $monthsAhead = 6): array
    {
        // Get historical data (both manual entries and system-generated)
        $historicalData = $this->getHistoricalTimeSeries();

        if (count($historicalData) < 12) {
            return [
                'success' => false,
                'message' => 'Insufficient historical data. Need at least 12 months of data for predictions.',
                'data_points' => count($historicalData),
            ];
        }

        // Calculate predictions using simplified SARIMA approach
        $predictions = $this->calculateSarimaPredictions($historicalData, $monthsAhead);

        return [
            'success' => true,
            'message' => "Generated {$monthsAhead} months of predictions",
            'predictions' => $predictions,
            'historical_data' => $historicalData,
        ];
    }

    /**
     * Get historical time series data for health card issuance
     */
    protected function getHistoricalTimeSeries(): array
    {
        // Get manual historical data
        $manualData = HistoricalHealthCardData::query()
            ->orderBy('record_date')
            ->get();

        // Get system-generated data from actual health card records
        $startDate = $manualData->last()?->record_date ?? Carbon::now()->subYears(2);
        $systemData = HealthCard::query()
            ->where('issue_date', '>=', $startDate)
            ->get()
            ->groupBy(function ($card) {
                return $card->issue_date->format('Y-m');
            })
            ->map(function ($group) {
                return (object) [
                    'period' => $group->first()->issue_date->format('Y-m'),
                    'issued_count' => $group->count(),
                ];
            });

        // Merge both datasets
        $timeSeries = [];

        foreach ($manualData as $data) {
            $period = $data->record_date->format('Y-m');
            $timeSeries[$period] = $data->issued_count;
        }

        foreach ($systemData as $data) {
            $period = Carbon::parse($data->period)->format('Y-m');
            if (! isset($timeSeries[$period])) {
                $timeSeries[$period] = $data->issued_count;
            }
        }

        ksort($timeSeries);

        return $timeSeries;
    }

    /**
     * Calculate SARIMA predictions using simplified approach
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
                'period' => $predictionDate->format('Y-m'),
                'label' => $predictionDate->format('M Y'),
                'predicted_issued' => round($predictedValue, 2),
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
     * Check if sufficient data exists for predictions
     */
    public function hasSufficientData(): array
    {
        $historicalData = $this->getHistoricalTimeSeries();
        $dataPoints = count($historicalData);

        return [
            'sufficient' => $dataPoints >= 12,
            'data_points' => $dataPoints,
            'required' => 12,
            'missing' => max(0, 12 - $dataPoints),
        ];
    }

    /**
     * Get combined historical and predicted data for charting
     * Returns full year (Jan-Dec) with predictions extending to December
     */
    public function getChartData(): array
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Create full year structure (Jan-Dec)
        $fullYear = [];
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($currentYear, $month, 1);
            $fullYear[$date->format('Y-m')] = [
                'label' => $date->format('M Y'),
                'actual' => 0,
                'predicted' => null,
                'confidence_lower' => null,
                'confidence_upper' => null,
            ];
        }

        // Get historical data
        $historicalData = $this->getHistoricalTimeSeries();

        // Fill in actual data for current year
        foreach ($historicalData as $period => $count) {
            $date = Carbon::parse($period);
            if ($date->year === $currentYear && isset($fullYear[$period])) {
                $fullYear[$period]['actual'] = $count;
            }
        }

        // Calculate months remaining until December
        $monthsToDecember = 12 - $currentMonth;

        // Try to generate predictions if we have sufficient data
        if (count($historicalData) >= 12 && $monthsToDecember > 0) {
            $result = $this->generatePredictions($monthsToDecember);

            if ($result['success']) {
                // Fill in predictions for remaining months
                foreach ($result['predictions'] as $prediction) {
                    $period = $prediction['period'];
                    if (isset($fullYear[$period])) {
                        $fullYear[$period]['predicted'] = $prediction['predicted_issued'];
                        $fullYear[$period]['confidence_lower'] = $prediction['confidence_interval_lower'];
                        $fullYear[$period]['confidence_upper'] = $prediction['confidence_interval_upper'];
                    }
                }

                return [
                    'labels' => array_column($fullYear, 'label'),
                    'actual' => array_column($fullYear, 'actual'),
                    'predicted' => array_column($fullYear, 'predicted'),
                    'confidence_lower' => array_column($fullYear, 'confidence_lower'),
                    'confidence_upper' => array_column($fullYear, 'confidence_upper'),
                    'has_predictions' => true,
                    'message' => $result['message'],
                ];
            }
        }

        // Return without predictions if insufficient data or already December
        return [
            'labels' => array_column($fullYear, 'label'),
            'actual' => array_column($fullYear, 'actual'),
            'predicted' => array_column($fullYear, 'predicted'),
            'confidence_lower' => array_column($fullYear, 'confidence_lower'),
            'confidence_upper' => array_column($fullYear, 'confidence_upper'),
            'has_predictions' => false,
            'message' => count($historicalData) < 12
                ? 'Insufficient historical data. Need at least 12 months of data for predictions.'
                : 'Viewing current year data',
        ];
    }
}
