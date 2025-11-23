import { Chart } from "chart.js";

let chartInstance = null;

function buildGradient(ctx, baseColor) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, baseColor.replace('1)', '0.25)'));
    gradient.addColorStop(1, baseColor.replace('1)', '0)'));
    return gradient;
}

function initHealthCardsChart() {
    const el = document.getElementById('health-cards-trend');
    if (!el) { return; }

    // Prevent double-initialization across Livewire morphs
    if (el.dataset.chartInited === 'true') { return; }

    const labels = JSON.parse(el.getAttribute('data-labels') || '[]');
    const actual = JSON.parse(el.getAttribute('data-actual') || '[]');
    const predicted = JSON.parse(el.getAttribute('data-predicted') || '[]');
    const confidenceLower = JSON.parse(el.getAttribute('data-confidence-lower') || '[]');
    const confidenceUpper = JSON.parse(el.getAttribute('data-confidence-upper') || '[]');
    const hasPredictions = el.getAttribute('data-has-predictions') === 'true';

    // No data, skip
    if (!Array.isArray(labels) || labels.length === 0) {
        return;
    }

    const ctx = el.getContext('2d');

    // Destroy previous global instance if exists
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
    const textColor = isDark ? 'rgba(255,255,255,0.8)' : 'rgba(0,0,0,0.6)';

    // Build datasets
    const datasets = [
        {
            label: 'Actual Issued',
            data: actual,
            borderColor: 'rgb(59, 130, 246)', // blue-500
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointRadius: 3,
            fill: true,
            tension: 0.35,
            spanGaps: false,
        }
    ];

    if (hasPredictions && predicted.some(v => v !== null)) {
        // Add predicted dataset
        datasets.push({
            label: 'Predicted (SARIMA)',
            data: predicted,
            borderColor: 'rgb(249, 115, 22)', // orange-500
            backgroundColor: 'rgba(249, 115, 22, 0.1)',
            pointBackgroundColor: 'rgb(249, 115, 22)',
            pointRadius: 3,
            borderDash: [5, 5],
            fill: false,
            tension: 0.35,
            spanGaps: true,
        });

        // Add confidence interval as a filled area (optional)
        if (confidenceUpper.some(v => v !== null)) {
            datasets.push({
                label: 'Confidence Interval',
                data: confidenceUpper,
                borderColor: 'rgba(249, 115, 22, 0)',
                backgroundColor: 'rgba(249, 115, 22, 0.15)',
                pointRadius: 0,
                fill: '+1', // Fill to the next dataset (lower bound)
                tension: 0.35,
                spanGaps: true,
            });

            datasets.push({
                label: 'Lower Bound',
                data: confidenceLower,
                borderColor: 'rgba(249, 115, 22, 0)',
                backgroundColor: 'rgba(249, 115, 22, 0)',
                pointRadius: 0,
                fill: false,
                tension: 0.35,
                spanGaps: true,
            });
        }
    }

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                        filter: function(item, chart) {
                            // Hide confidence interval labels from legend
                            return !item.text.includes('Bound') && !item.text.includes('Confidence Interval');
                        }
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            if (ctx.dataset.label.includes('Bound') || ctx.dataset.label.includes('Confidence Interval')) {
                                return null;
                            }
                            const v = ctx.formattedValue ?? ctx.raw;
                            if (v === null || v === 'null') {
                                return null;
                            }
                            return ` ${ctx.dataset.label}: ${v}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        callback: function(value) {
                            return Math.round(value);
                        }
                    },
                    grid: { color: gridColor },
                }
            }
        }
    });

    el.dataset.chartInited = 'true';
}

function chartInit() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHealthCardsChart, { once: true });
    } else {
        initHealthCardsChart();
    }

    // Re-init after Livewire DOM updates
    document.addEventListener('livewire:navigated', () => {
        setTimeout(initHealthCardsChart, 100);
    });

    // console.log('Health Cards chart init scheduled.');
}

chartInit();
