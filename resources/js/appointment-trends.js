import { Chart } from 'chart.js';

let appointmentTrendsChartInstance = null;

export function initAppointmentTrendsChart() {
    const canvas = document.getElementById('appointmentTrendsChart');
    if (!canvas) {
        return;
    }

    // Prevent double-initialization
    if (canvas.dataset.chartInited === 'true') {
        return;
    }

    const chartData = JSON.parse(canvas.getAttribute('data-chart') || '{}');

    if (!chartData.labels || chartData.labels.length === 0) {
        return;
    }

    const ctx = canvas.getContext('2d');

    // Destroy previous instance if exists
    if (appointmentTrendsChartInstance) {
        appointmentTrendsChartInstance.destroy();
        appointmentTrendsChartInstance = null;
    }

    appointmentTrendsChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets
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
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    bodySpacing: 6,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' appointments';

                            // Add prediction indicator for last two data points
                            const dataLength = context.dataset.data.length;
                            if (context.dataIndex === dataLength - 1 || context.dataIndex === dataLength - 2) {
                                label += ' (Predicted)';
                            }
                            return label;
                        }
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Number of Appointments',
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    canvas.dataset.chartInited = 'true';
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', initAppointmentTrendsChart);

// Re-initialize after Livewire navigation
document.addEventListener('livewire:navigated', initAppointmentTrendsChart);
