<?php

namespace App\Services;

use App\Models\Barangay;
use App\Models\Disease;
use App\Models\DiseasePrediction;
use App\Models\HistoricalDiseaseData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiseaseSurveillanceService
{
    /**
     * Get disease statistics for a given time period.
     */
    public function getStatistics(?string $diseaseType = null, string $period = '30days'): array
    {
        $dateRange = $this->getDateRange($period);

        $query = Disease::confirmed()
            ->betweenDates($dateRange['start'], $dateRange['end']);

        if ($diseaseType) {
            $query->ofType($diseaseType);
        }

        // Confirmed disease cases from live data
        $liveCases = $query->count();

        // Historical cases within the same range
        $historicalCases = HistoricalDiseaseData::query()
            ->when($diseaseType, fn ($q) => $q->where('disease_type', $diseaseType))
            ->whereBetween('record_date', [$dateRange['start'], $dateRange['end']])
            ->sum('case_count');

        $totalCases = $liveCases + (int) $historicalCases;
        $newCases = Disease::confirmed()
            ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
            ->where('reported_date', '>=', now()->subDays(7))
            ->count();

        $highRiskBarangays = $this->getHighRiskBarangays($diseaseType, $period);
        $trendDirection = $this->calculateTrendDirection($diseaseType, $period);

        // Get disease breakdown by type
        $diseaseBreakdown = Disease::confirmed()
            ->betweenDates($dateRange['start'], $dateRange['end'])
            ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
            ->selectRaw('disease_type, COUNT(*) as count')
            ->groupBy('disease_type')
            ->pluck('count', 'disease_type')
            ->toArray();

        // Historical breakdown by disease type
        $historicalBreakdown = HistoricalDiseaseData::query()
            ->whereBetween('record_date', [$dateRange['start'], $dateRange['end']])
            ->when($diseaseType, fn ($q) => $q->where('disease_type', $diseaseType))
            ->select('disease_type', DB::raw('SUM(case_count) as count'))
            ->groupBy('disease_type')
            ->pluck('count', 'disease_type')
            ->toArray();

        // Merge both breakdowns by summing counts
        foreach ($historicalBreakdown as $type => $count) {
            $diseaseBreakdown[$type] = ($diseaseBreakdown[$type] ?? 0) + (int) $count;
        }

        return [
            'total_cases' => $totalCases,
            'new_cases_7days' => $newCases,
            'high_risk_barangays' => $highRiskBarangays,
            'trend_direction' => $trendDirection,
            'period' => $period,
            'disease_breakdown' => $diseaseBreakdown,
        ];
    }

    /**
     * Get heatmap data for disease visualization.
     */
    public function getHeatmapData(?string $diseaseType = null, string $period = '30days'): array
    {
        $dateRange = $this->getDateRange($period);

        $barangays = Barangay::all();
        $heatmapData = [];

        foreach ($barangays as $barangay) {
            $liveCount = Disease::confirmed()
                ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
                ->inBarangay($barangay->id)
                ->betweenDates($dateRange['start'], $dateRange['end'])
                ->count();

            $historicalCount = HistoricalDiseaseData::query()
                ->when($diseaseType, fn ($q) => $q->where('disease_type', $diseaseType))
                ->where('barangay_id', $barangay->id)
                ->whereBetween('record_date', [$dateRange['start'], $dateRange['end']])
                ->sum('case_count');

            $casesCount = $liveCount + (int) $historicalCount;

            $heatmapData[] = [
                'barangay_id' => $barangay->id,
                'barangay_name' => $barangay->name,
                'latitude' => $barangay->latitude,
                'longitude' => $barangay->longitude,
                'cases_count' => $casesCount,
                'intensity_level' => $this->getIntensityLevel($casesCount),
                'color' => $this->getColorCode($casesCount),
            ];
        }

        return $heatmapData;
    }

    /**
     * Get trend analysis data with historical and predicted values.
     */
    public function getTrendAnalysis(string $diseaseType, int $monthsBack = 12): array
    {
        $historicalData = $this->getHistoricalTrend($diseaseType, $monthsBack);
        $predictedData = $this->getPredictedTrend($diseaseType);

        return [
            'historical' => $historicalData,
            'predicted' => $predictedData,
            'disease_type' => $diseaseType,
        ];
    }

    /**
     * Get high-risk barangays based on case counts.
     */
    public function getHighRiskBarangays(?string $diseaseType = null, string $period = '30days', int $limit = 5): array
    {
        $dateRange = $this->getDateRange($period);

        $highRisk = Disease::confirmed()
            ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
            ->betweenDates($dateRange['start'], $dateRange['end'])
            ->select('barangay_id', DB::raw('count(*) as cases_count'))
            ->groupBy('barangay_id')
            ->orderByDesc('cases_count')
            ->limit($limit)
            ->with('barangay')
            ->get()
            ->map(fn ($item) => [
                'barangay_name' => $item->barangay?->name ?? 'Unknown',
                'cases_count' => $item->cases_count,
                'risk_level' => $this->getRiskLevel($item->cases_count),
            ])
            ->toArray();

        return $highRisk;
    }

    /**
     * Detect potential outbreaks based on threshold and trends.
     */
    public function detectOutbreaks(?string $diseaseType = null): array
    {
        $outbreaks = [];
        $barangays = Barangay::all();

        foreach ($barangays as $barangay) {
            $recentCases = Disease::confirmed()
                ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
                ->inBarangay($barangay->id)
                ->where('reported_date', '>=', now()->subDays(7))
                ->count();

            $previousCases = Disease::confirmed()
                ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
                ->inBarangay($barangay->id)
                ->whereBetween('reported_date', [now()->subDays(14), now()->subDays(7)])
                ->count();

            // Alert if recent cases exceed threshold or show significant increase
            if ($recentCases >= 5 || ($previousCases > 0 && $recentCases / $previousCases >= 2)) {
                $outbreaks[] = [
                    'barangay_id' => $barangay->id,
                    'barangay_name' => $barangay->name,
                    'recent_cases' => $recentCases,
                    'previous_cases' => $previousCases,
                    'increase_rate' => $previousCases > 0 ? round(($recentCases - $previousCases) / $previousCases * 100, 2) : 100,
                    'risk_level' => $this->getRiskLevel($recentCases),
                    'detected_at' => now()->toDateTimeString(),
                ];
            }
        }

        return $outbreaks;
    }

    /**
     * Get disease types available for tracking.
     */
    public function getAvailableDiseaseTypes(): array
    {
        return [
            'hiv' => 'HIV/AIDS',
            'dengue' => 'Dengue',
            'malaria' => 'Malaria',
            'measles' => 'Measles',
            'rabies' => 'Rabies',
            'pregnancy_complications' => 'Pregnancy Complications',
        ];
    }

    /**
     * Get historical trend data for a disease.
     */
    protected function getHistoricalTrend(string $diseaseType, int $monthsBack): array
    {
        $data = [];
        $startDate = now()->subMonths($monthsBack)->startOfMonth();

        for ($i = 0; $i < $monthsBack; $i++) {
            $monthStart = $startDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $liveCount = Disease::confirmed()
                ->ofType($diseaseType)
                ->betweenDates($monthStart, $monthEnd)
                ->count();

            $historicalCount = HistoricalDiseaseData::query()
                ->where('disease_type', $diseaseType)
                ->whereBetween('record_date', [$monthStart, $monthEnd])
                ->sum('case_count');

            $casesCount = $liveCount + (int) $historicalCount;

            $data[] = [
                'date' => $monthStart->format('Y-m'),
                'month' => $monthStart->format('M Y'),
                'cases' => $casesCount,
            ];
        }

        return $data;
    }

    /**
     * Get predicted trend data from disease predictions.
     */
    protected function getPredictedTrend(string $diseaseType): array
    {
        $predictions = DiseasePrediction::where('disease_type', $diseaseType)
            ->whereNull('barangay_id') // City-wide predictions
            ->where('prediction_date', '>=', now()->startOfMonth())
            ->orderBy('prediction_date')
            ->limit(6)
            ->get()
            ->map(fn ($prediction) => [
                'date' => $prediction->prediction_date->format('Y-m'),
                'month' => $prediction->prediction_date->format('M Y'),
                'predicted_cases' => $prediction->predicted_cases,
                'lower_bound' => $prediction->confidence_interval_lower,
                'upper_bound' => $prediction->confidence_interval_upper,
            ])
            ->toArray();

        return $predictions;
    }

    /**
     * Calculate trend direction (up, down, stable).
     */
    protected function calculateTrendDirection(?string $diseaseType = null, string $period = '30days'): string
    {
        $dateRange = $this->getDateRange($period);
        $midpoint = Carbon::parse($dateRange['start'])->addDays(
            Carbon::parse($dateRange['start'])->diffInDays($dateRange['end']) / 2
        );

        $firstHalfLive = Disease::confirmed()
            ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
            ->betweenDates($dateRange['start'], $midpoint)
            ->count();

        $firstHalfHistorical = HistoricalDiseaseData::query()
            ->when($diseaseType, fn ($q) => $q->where('disease_type', $diseaseType))
            ->whereBetween('record_date', [$dateRange['start'], $midpoint])
            ->sum('case_count');

        $firstHalfCases = $firstHalfLive + (int) $firstHalfHistorical;

        $secondHalfLive = Disease::confirmed()
            ->when($diseaseType, fn ($q) => $q->ofType($diseaseType))
            ->betweenDates($midpoint, $dateRange['end'])
            ->count();

        $secondHalfHistorical = HistoricalDiseaseData::query()
            ->when($diseaseType, fn ($q) => $q->where('disease_type', $diseaseType))
            ->whereBetween('record_date', [$midpoint, $dateRange['end']])
            ->sum('case_count');

        $secondHalfCases = $secondHalfLive + (int) $secondHalfHistorical;

        if ($secondHalfCases > $firstHalfCases * 1.2) {
            return 'up';
        }

        if ($secondHalfCases < $firstHalfCases * 0.8) {
            return 'down';
        }

        return 'stable';
    }

    /**
     * Get date range based on period string.
     */
    protected function getDateRange(string $period): array
    {
        // Support selecting a specific calendar year via "year:YYYY"
        if (str_starts_with($period, 'year:')) {
            $year = (int) str_replace('year:', '', $period);
            if ($year > 0) {
                $start = Carbon::create($year, 1, 1, 0, 0, 0);
                $end = Carbon::create($year, 12, 31, 23, 59, 59);

                return [
                    'start' => $start,
                    'end' => $end,
                ];
            }
        }

        return match ($period) {
            '7days' => [
                'start' => now()->subDays(7),
                'end' => now(),
            ],
            '30days' => [
                'start' => now()->subDays(30),
                'end' => now(),
            ],
            '90days' => [
                'start' => now()->subDays(90),
                'end' => now(),
            ],
            '1year' => [
                'start' => now()->subYear(),
                'end' => now(),
            ],
            default => [
                'start' => now()->subDays(30),
                'end' => now(),
            ],
        };
    }

    /**
     * Return all available years from both diseases and historical data.
     * Sorted descending (most recent first).
     *
     * @return array<int>
     */
    public function getAvailableYears(): array
    {
        $driver = DB::getDriverName();

        $yearExprDisease = match ($driver) {
            'sqlite' => "CAST(strftime('%Y', diagnosis_date) AS INTEGER)",
            'pgsql' => 'EXTRACT(YEAR FROM diagnosis_date)',
            default => 'YEAR(diagnosis_date)', // mysql, mariadb, etc.
        };

        $yearExprHistorical = match ($driver) {
            'sqlite' => "CAST(strftime('%Y', record_date) AS INTEGER)",
            'pgsql' => 'EXTRACT(YEAR FROM record_date)',
            default => 'YEAR(record_date)',
        };

        $diseaseYears = Disease::query()
            ->selectRaw($yearExprDisease.' as year')
            ->whereNotNull('diagnosis_date')
            ->distinct()
            ->pluck('year')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->all();

        $historicalYears = HistoricalDiseaseData::query()
            ->selectRaw($yearExprHistorical.' as year')
            ->whereNotNull('record_date')
            ->distinct()
            ->pluck('year')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->all();

        $years = array_values(array_unique(array_merge($diseaseYears, $historicalYears)));
        rsort($years);

        return $years;
    }

    /**
     * Get intensity level based on case count.
     */
    protected function getIntensityLevel(int $casesCount): string
    {
        return match (true) {
            $casesCount === 0 => 'none',
            $casesCount <= 2 => 'low',
            $casesCount <= 5 => 'medium',
            $casesCount <= 10 => 'high',
            default => 'critical',
        };
    }

    /**
     * Get color code for heatmap based on case count.
     */
    protected function getColorCode(int $casesCount): string
    {
        return match (true) {
            $casesCount === 0 => '#10b981', // green
            $casesCount <= 2 => '#fbbf24', // yellow
            $casesCount <= 5 => '#f97316', // orange
            $casesCount <= 10 => '#ef4444', // red
            default => '#991b1b', // dark red
        };
    }

    /**
     * Get risk level based on case count.
     */
    protected function getRiskLevel(int $casesCount): string
    {
        return match (true) {
            $casesCount === 0 => 'None',
            $casesCount <= 2 => 'Low',
            $casesCount <= 5 => 'Moderate',
            $casesCount <= 10 => 'High',
            default => 'Critical',
        };
    }
}
