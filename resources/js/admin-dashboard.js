import Chart from 'chart.js/auto';

let chartInstance = null;

function buildGradient(ctx, baseColor) {
  const gradient = ctx.createLinearGradient(0, 0, 0, 180);
  gradient.addColorStop(0, baseColor.replace('1)', '0.25)'));
  gradient.addColorStop(1, baseColor.replace('1)', '0)'));
  return gradient;
}

function initWeeklyAppointmentsChart() {
  const el = document.getElementById('weekly-appointments-chart');
  if (!el) { return; }

  // Prevent double-initialization across Livewire morphs
  if (el.dataset.chartInited === 'true') { return; }

  const labels = JSON.parse(el.getAttribute('data-labels') || '[]');
  const values = JSON.parse(el.getAttribute('data-values') || '[]');

  // No data, skip
  if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0 || values.length === 0) {
    return;
  }

  const ctx = el.getContext('2d');

  // Destroy previous global instance if exists
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }

  const primaryRGBA = 'rgba(37, 99, 235, 1)'; // blue-600
  const gradient = buildGradient(ctx, 'rgba(37, 99, 235, 1)');

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Appointments',
        data: values,
        borderColor: primaryRGBA,
        backgroundColor: gradient,
        pointBackgroundColor: 'rgb(59, 130, 246)',
        tension: 0.35,
        fill: true,
        pointRadius: 3,
        pointHoverRadius: 4,
        borderWidth: 2,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: getComputedStyle(document.documentElement).color },
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(148, 163, 184, 0.15)' },
          ticks: { precision: 0 },
        },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ${ctx.parsed.y} appointments`,
          },
        },
      },
    },
  });

  el.dataset.chartInited = 'true';
}

function scheduleInit() {
  // Initialize once DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWeeklyAppointmentsChart, { once: true });
  } else {
    initWeeklyAppointmentsChart();
  }

  // Re-init after Livewire DOM updates
  document.addEventListener('livewire:navigated', () => {
    setTimeout(initWeeklyAppointmentsChart, 100);
  });
}

scheduleInit();
