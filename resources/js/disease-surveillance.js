import L from 'leaflet';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

// Alpine.js data function for surveillance component
window.initSurveillanceData = function(statistics, heatmapData, trendsData) {
    return {
        statistics: statistics,
        heatmapData: heatmapData,
        trendsData: trendsData,
        updateGlobalData() {
            window.surveillanceData = {
                statistics: this.statistics,
                heatmapData: this.heatmapData,
                trendsData: this.trendsData
            };
            console.log('Surveillance data updated:', window.surveillanceData);
            console.log('Disease breakdown:', window.surveillanceData.statistics?.disease_breakdown);
            console.log('Trends data length:', window.surveillanceData.trendsData?.length);

            // Reset initialization flag to allow reinitialization
            window.dashboardInitialized = false;

            // Dispatch event to notify charts are ready to initialize
            window.dispatchEvent(new CustomEvent('surveillance-data-ready'));
        }
    };
};

// Initialize Disease Surveillance Dashboard
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, waiting for surveillance data...');
    initializeListeners();
});

// Listen for Livewire navigation (SPA-style navigation)
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated, reinitializing...');
    initializeListeners();
});

function initializeListeners() {
    // Wait for surveillance data to be ready
    window.addEventListener('surveillance-data-ready', () => {
        console.log('Surveillance data ready event received');
        initializeDashboard();
    });

    // Fallback: try after a delay if event doesn't fire
    setTimeout(() => {
        if (window.surveillanceData && !window.dashboardInitialized) {
            console.log('Initializing via fallback timer');
            initializeDashboard();
        }
    }, 1000);
}function initializeDashboard() {
    if (window.dashboardInitialized) {
        console.log('Dashboard already initialized');
        return;
    }

    window.dashboardInitialized = true;
    console.log('Initializing dashboard...');

    // Initialize the current active tab
    initializeActiveTab();

    // Listen for tab changes
    if (typeof Livewire !== 'undefined') {
        Livewire.on('tab-changed', (data) => {
            setTimeout(() => {
                const tab = Array.isArray(data) ? data[0]?.tab : data?.tab;
                console.log('Tab changed to:', tab);
                if (tab === 'heatmap') {
                    initHeatmap();
                } else if (tab === 'overview') {
                    initOverviewChart();
                } else if (tab === 'trends') {
                    initTrendsChart();
                }
            }, 300);
        });
    }
}

// Initialize the currently active tab
function initializeActiveTab() {
    console.log('Initializing active tab...');
    console.log('Surveillance data:', window.surveillanceData);

    const overviewChart = document.getElementById('disease-overview-chart');
    const heatmapDiv = document.getElementById('disease-heatmap');
    const trendsChart = document.getElementById('disease-trends-chart');

    if (overviewChart && overviewChart.offsetParent !== null) {
        console.log('Initializing overview chart');
        initOverviewChart();
    } else if (heatmapDiv && heatmapDiv.offsetParent !== null) {
        console.log('Initializing heatmap');
        initHeatmap();
    } else if (trendsChart && trendsChart.offsetParent !== null) {
        console.log('Initializing trends chart');
        initTrendsChart();
    } else {
        console.log('No visible chart/map container found, retrying...');
        // Retry after a short delay if no container is found
        // This handles cases where Livewire is still morphing the DOM
        setTimeout(() => {
            const retryTrendsChart = document.getElementById('disease-trends-chart');
            if (retryTrendsChart && retryTrendsChart.offsetParent !== null) {
                console.log('Retry successful - initializing trends chart');
                initTrendsChart();
            }
        }, 150);
    }
}

// Initialize Leaflet Heatmap
function initHeatmap() {
    const mapContainer = document.getElementById('disease-heatmap');
    if (!mapContainer || mapContainer._leaflet_id) return;

    // Create map centered on Panabo City, Davao del Norte
    const map = L.map('disease-heatmap').setView([7.5119, 125.6838], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Get heatmap data from global variable
    const heatmapData = window.surveillanceData?.heatmapData || [];

    // Add markers for each barangay with cases
    heatmapData.forEach(item => {
        if (item.latitude && item.longitude && item.cases_count > 0) {
            const color = getHeatmapColor(item.cases_count);
            const radius = Math.max(10, Math.min(30, item.cases_count * 2));

            const circle = L.circleMarker([item.latitude, item.longitude], {
                radius: radius,
                fillColor: color,
                color: color,
                weight: 2,
                opacity: 0.8,
                fillOpacity: 0.6
            }).addTo(map);

            circle.bindPopup(`
                <div class="p-2">
                    <strong>${item.barangay_name}</strong><br>
                    Cases: <strong>${item.cases_count}</strong><br>
                    Intensity: <strong>${item.intensity}</strong>
                </div>
            `);
        }
    });
}

// Get color based on case count
function getHeatmapColor(cases) {
    if (cases === 0) return '#22c55e'; // green
    if (cases <= 2) return '#eab308'; // yellow
    if (cases <= 5) return '#f97316'; // orange
    if (cases <= 10) return '#ef4444'; // red
    return '#7f1d1d'; // dark red
}

// Initialize Overview Chart (Disease Distribution)
function initOverviewChart() {
    console.log('initOverviewChart called');
    const ctx = document.getElementById('disease-overview-chart');
    if (!ctx) {
        console.warn('Chart canvas not found');
        return;
    }

    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        console.log('Destroying existing chart');
        existingChart.destroy();
    }

    // Get statistics from global variable
    const statistics = window.surveillanceData?.statistics;
    console.log('Statistics:', statistics);
    console.log('Disease breakdown:', statistics?.disease_breakdown);

    if (!statistics) {
        console.warn('No statistics available');
        return;
    }

    // Check if disease_breakdown exists and has data
    const diseaseBreakdown = statistics.disease_breakdown;
    if (!diseaseBreakdown || Object.keys(diseaseBreakdown).length === 0) {
        console.warn('No disease breakdown data available');
        return;
    }

    // Prepare data
    const labels = Object.keys(diseaseBreakdown).map(key =>
        key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
    );
    const data = Object.values(diseaseBreakdown);

    console.log('Chart labels:', labels);
    console.log('Chart data:', data);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Cases',
                data: data,
                backgroundColor: [
                    'rgba(239, 68, 68, 0.7)',   // Red
                    'rgba(249, 115, 22, 0.7)',  // Orange
                    'rgba(234, 179, 8, 0.7)',   // Yellow
                    'rgba(34, 197, 94, 0.7)',   // Green
                    'rgba(59, 130, 246, 0.7)',  // Blue
                    'rgba(168, 85, 247, 0.7)'   // Purple
                ],
                borderColor: [
                    'rgb(239, 68, 68)',
                    'rgb(249, 115, 22)',
                    'rgb(234, 179, 8)',
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(168, 85, 247)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Disease Distribution by Type'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Initialize Trends Chart (Time Series)
function initTrendsChart() {
    console.log('initTrendsChart called');
    const ctx = document.getElementById('disease-trends-chart');
    if (!ctx) {
        console.warn('Trends chart canvas not found');
        return;
    }

    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        console.log('Destroying existing trends chart');
        existingChart.destroy();
    }

    // Get trends data from global variable
    const trendsData = window.surveillanceData?.trendsData || [];
    console.log('Trends data:', trendsData);

    if (!trendsData || trendsData.length === 0) {
        console.warn('No trends data available - disease type filter may be required');
        return;
    }

    // Group data by disease type
    const diseaseTypes = [...new Set(trendsData.map(item => item.disease_type))];
    const datasets = diseaseTypes.map((diseaseType, index) => {
        const filtered = trendsData.filter(item => item.disease_type === diseaseType);
        const colors = [
            'rgb(239, 68, 68)',   // Red
            'rgb(249, 115, 22)',  // Orange
            'rgb(234, 179, 8)',   // Yellow
            'rgb(34, 197, 94)',   // Green
            'rgb(59, 130, 246)',  // Blue
            'rgb(168, 85, 247)'   // Purple
        ];

        return {
            label: diseaseType.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' '),
            data: filtered.map(item => ({ x: item.period, y: item.cases_count })),
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
            tension: 0.3
        };
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Disease Trends Over Time'
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: true,
                        text: 'Time Period'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Cases'
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
