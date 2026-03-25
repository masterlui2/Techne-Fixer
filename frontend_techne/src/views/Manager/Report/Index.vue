<!-- src/views/admin/ReportsView.vue -->
<template>
  <div class="reports-page">

    <!-- ── Header ── -->
    <div class="reports-header">
      <div class="header-brand">
        <div class="header-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <path d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <h1 class="header-title">Reports</h1>
          <p class="header-sub">Track revenue, job orders, and business performance</p>
        </div>
      </div>

      <button class="btn-export" :disabled="exporting" @click="exportCSV">
        <svg v-if="!exporting" width="15" height="15" viewBox="0 0 24 24" fill="none">
          <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg v-else class="spinner" width="15" height="15" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        {{ exporting ? 'Exporting…' : 'Export CSV' }}
      </button>
    </div>

    <!-- ── Period Filters ── -->
    <div class="filter-bar">
      <div class="period-pills">
        <span class="period-label">Period:</span>
        <button
          v-for="p in periods"
          :key="p.value"
          class="period-btn"
          :class="{ 'period-btn--active': filters.period === p.value }"
          @click="setPeriod(p.value)"
        >{{ p.label }}</button>
      </div>

      <div class="custom-range">
        <span class="period-label">Custom:</span>
        <input
          v-model="filters.date_from"
          type="date"
          class="date-input"
          @change="setPeriod('custom')"
        />
        <span class="date-sep">–</span>
        <input
          v-model="filters.date_to"
          type="date"
          class="date-input"
          @change="setPeriod('custom')"
        />
        <button class="btn-apply" @click="fetchReports">Apply</button>
      </div>
    </div>

    <!-- ── Summary Cards ── -->
    <div class="summary-grid">
      <div class="summary-card">
        <p class="summary-label">Total Revenue</p>
        <p class="summary-amount">{{ formatCurrency(stats.reports.total_revenue) }}</p>
        <p class="summary-hint summary-hint--blue">Jobs + diagnostic fees</p>
      </div>
      <div class="summary-card">
        <p class="summary-label">Completed Job Revenue</p>
        <p class="summary-amount">{{ formatCurrency(stats.reports.completed_jobs_revenue) }}</p>
        <p class="summary-hint summary-hint--green">From completed jobs</p>
      </div>
      <div class="summary-card">
        <p class="summary-label">Downpayments Collected</p>
        <p class="summary-amount">{{ formatCurrency(stats.reports.downpayments_received) }}</p>
        <p class="summary-hint summary-hint--amber">50% of completed jobs</p>
      </div>
      <div class="summary-card">
        <p class="summary-label">Diagnostic Fees</p>
        <p class="summary-amount">{{ formatCurrency(stats.reports.diagnostic_fees) }}</p>
        <p class="summary-hint summary-hint--violet">Upfront fees collected</p>
      </div>
    </div>

    <!-- ── Revenue Chart ── -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="card-title">Revenue Overview</h3>
          <p class="card-sub">Daily revenue breakdown for selected period</p>
        </div>
      </div>
      <div class="chart-wrapper">
        <canvas ref="chartCanvas"></canvas>
      </div>
    </div>

    <!-- ── Insights Row ── -->
    <div class="insights-grid">

      <!-- Revenue Breakdown -->
      <div class="insight-card">
        <div class="insight-header">
          <h3 class="card-title">Revenue Breakdown</h3>
          <span class="insight-tag">Actual collections</span>
        </div>
        <div class="breakdown-list">
          <div class="breakdown-item">
            <span>Completed job orders</span>
            <span class="breakdown-value">{{ formatCurrency(stats.reports.completed_jobs_revenue) }}</span>
          </div>
          <div class="breakdown-item">
            <span>Downpayments (50%)</span>
            <span class="breakdown-value">{{ formatCurrency(stats.reports.downpayments_received) }}</span>
          </div>
          <div class="breakdown-item">
            <span>Remaining balance (50%)</span>
            <span class="breakdown-value">{{ formatCurrency(stats.reports.remaining_balance) }}</span>
          </div>
          <div class="breakdown-item breakdown-item--divider">
            <span>Diagnostic fees</span>
            <span class="breakdown-value">{{ formatCurrency(stats.reports.diagnostic_fees) }}</span>
          </div>
          <div class="breakdown-item breakdown-item--divider breakdown-item--total">
            <span>Total revenue</span>
            <span class="breakdown-value breakdown-value--green">{{ formatCurrency(stats.reports.total_revenue) }}</span>
          </div>
        </div>
      </div>

      <!-- Job Order Stats -->
      <div class="insight-card">
        <div class="insight-header">
          <h3 class="card-title">Job Order Statistics</h3>
        </div>
        <div class="breakdown-list">
          <div class="breakdown-item">
            <div class="stat-dot-row">
              <span class="dot dot--gray"></span>
              <span>Total jobs</span>
            </div>
            <span class="breakdown-value">{{ stats.counts.total_jobs }}</span>
          </div>
          <div class="breakdown-item">
            <div class="stat-dot-row">
              <span class="dot dot--amber"></span>
              <span>Active jobs</span>
            </div>
            <span class="breakdown-value breakdown-value--amber">{{ stats.counts.active_jobs }}</span>
          </div>
          <div class="breakdown-item">
            <div class="stat-dot-row">
              <span class="dot dot--green"></span>
              <span>Completed jobs</span>
            </div>
            <span class="breakdown-value breakdown-value--green">{{ stats.counts.completed_jobs }}</span>
          </div>
          <div class="breakdown-item breakdown-item--divider">
            <span>Quotation approval rate</span>
            <span class="breakdown-value">{{ stats.reports.approval_rate ?? 0 }}%</span>
          </div>
          <div class="breakdown-item">
            <span>Avg. job value</span>
            <span class="breakdown-value">{{ formatCurrency(stats.reports.avg_job_value) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Recent Job Orders ── -->
    <div class="table-card">
      <div class="table-card-header">
        <div>
          <h3 class="card-title">Recent Job Orders</h3>
          <p class="card-sub">Latest completed jobs across all technicians</p>
        </div>
      </div>

      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        Loading…
      </div>

      <div v-else class="table-scroll">
        <table class="r-table">
          <thead>
            <tr>
              <th class="th" style="width:12%">Job #</th>
              <th class="th" style="width:30%">Customer</th>
              <th class="th" style="width:25%">Technician</th>
              <th class="th th--center" style="width:13%">Status</th>
              <th class="th th--right" style="width:20%">Amount</th>
            </tr>
          </thead>
          <tbody>
            <template v-if="recentJobs.length > 0">
              <tr v-for="job in recentJobs" :key="job.id" class="table-row">
                <td class="td"><span class="job-id">JOB-{{ String(job.id).padStart(4, '0') }}</span></td>
                <td class="td">{{ job.quotation?.client_name ?? job.customer_name ?? '—' }}</td>
                <td class="td">{{ job.technician?.name ?? '—' }}</td>
                <td class="td td--center">
                  <span class="status-badge" :class="`status-badge--${job.status}`">
                    {{ formatStatus(job.status) }}
                  </span>
                </td>
                <td class="td td--right font-semibold">{{ formatCurrency(job.subtotal) }}</td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="5" class="td--empty">No job orders found for this period.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Bottom Row: Top Revenue + Technician Performance ── -->
    <div class="bottom-grid">

      <!-- Top Revenue Jobs -->
      <div class="table-card">
        <div class="table-card-header">
          <div>
            <h3 class="card-title">Top Revenue Jobs</h3>
            <p class="card-sub">Highest earning jobs</p>
          </div>
        </div>

        <div v-if="topRevenueJobs.length > 0" class="top-jobs-list">
          <div v-for="job in topRevenueJobs" :key="job.id" class="top-job-item">
            <div class="top-job-left">
              <p class="top-job-title">{{ job.quotation?.project_title ?? 'Job Order' }}</p>
              <p class="top-job-id">JOB-{{ String(job.id).padStart(4, '0') }}</p>
            </div>
            <span class="status-badge status-badge--completed flex-shrink-0">Completed</span>
            <div class="top-job-footer">
              <span class="top-job-client">{{ job.quotation?.client_name ?? 'Customer' }}</span>
              <span class="top-job-amount">{{ formatCurrency(job.calculated_subtotal ?? job.subtotal) }}</span>
            </div>
          </div>
        </div>
        <p v-else class="empty-msg">No completed jobs yet.</p>
      </div>

      <!-- Technician Performance -->
      <div class="table-card">
        <div class="table-card-header">
          <div>
            <h3 class="card-title">Technician Performance</h3>
            <p class="card-sub">Revenue and job completion by technician</p>
          </div>
        </div>

        <div class="table-scroll">
          <table class="r-table">
            <thead>
              <tr>
                <th class="th" style="width:30%">Technician</th>
                <th class="th th--center" style="width:12%">Total</th>
                <th class="th th--center" style="width:12%">Done</th>
                <th class="th th--center" style="width:12%">Active</th>
                <th class="th th--right" style="width:18%">Revenue</th>
                <th class="th th--right" style="width:16%">Avg.</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="technicianPerformance.length > 0">
                <tr v-for="tech in technicianPerformance" :key="tech.id" class="table-row">
                  <td class="td font-semibold">{{ tech.name }}</td>
                  <td class="td td--center">{{ tech.total_jobs ?? 0 }}</td>
                  <td class="td td--center text-green">{{ tech.completed_jobs ?? 0 }}</td>
                  <td class="td td--center text-amber">{{ tech.active_jobs ?? 0 }}</td>
                  <td class="td td--right font-semibold">{{ formatCurrency(tech.total_revenue) }}</td>
                  <td class="td td--right">{{ formatCurrency(tech.avg_job_value) }}</td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="6" class="td--empty">No technician data available.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch, nextTick } from 'vue';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── Refs ──────────────────────────────────────────────────────────────────────
const loading    = ref(true);
const exporting  = ref(false);
const chartCanvas = ref(null);
let   chartInstance = null;

const stats = ref({
  reports: {
    total_revenue:          0,
    completed_jobs_revenue: 0,
    downpayments_received:  0,
    diagnostic_fees:        0,
    remaining_balance:      0,
    approval_rate:          0,
    avg_job_value:          0,
  },
  counts: {
    total_jobs:     0,
    active_jobs:    0,
    completed_jobs: 0,
  },
});

const chartData           = ref({ labels: [], revenue: [], completed_revenue: [], diagnostic_fees: [] });
const recentJobs          = ref([]);
const topRevenueJobs      = ref([]);
const technicianPerformance = ref([]);

// ── Filters ───────────────────────────────────────────────────────────────────
const filters = reactive({
  period:    'all_time',
  date_from: '',
  date_to:   '',
});

const periods = [
  { value: 'all_time',   label: 'All Time'   },
  { value: 'this_week',  label: 'This Week'  },
  { value: 'this_month', label: 'This Month' },
  { value: 'last_month', label: 'Last Month' },
  { value: 'this_year',  label: 'This Year'  },
];

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchReports = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      period: filters.period,
      ...(filters.date_from && { date_from: filters.date_from }),
      ...(filters.date_to   && { date_to:   filters.date_to   }),
    });

    const res = await fetch(`${API_URL}/admin/reports?${params}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();

    if (data.stats)   stats.value = data.stats;
    if (data.chart)   chartData.value = data.chart;

    recentJobs.value           = data.recent_jobs            ?? [];
    topRevenueJobs.value       = data.top_revenue_jobs        ?? [];
    technicianPerformance.value = data.technician_performance ?? [];

    await nextTick();
    renderChart();

  } catch (err) {
    console.error('Failed to fetch reports:', err);
  } finally {
    loading.value = false;
  }
};

// ── Chart ─────────────────────────────────────────────────────────────────────
const renderChart = async () => {
  if (!chartCanvas.value) return;

  // Dynamically import Chart.js
  const { Chart, registerables } = await import('chart.js');
  Chart.register(...registerables);

  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }

  chartInstance = new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels: chartData.value.labels ?? [],
      datasets: [
        {
          label: 'Total Revenue',
          data: chartData.value.revenue ?? [],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.08)',
          tension: 0.4,
          fill: true,
          pointRadius: 3,
        },
        {
          label: 'Completed Jobs Revenue',
          data: chartData.value.completed_revenue ?? [],
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.08)',
          tension: 0.4,
          fill: true,
          pointRadius: 3,
        },
        {
          label: 'Diagnostic Fees',
          data: chartData.value.diagnostic_fees ?? [],
          borderColor: '#a855f7',
          backgroundColor: 'rgba(168, 85, 247, 0.08)',
          tension: 0.4,
          fill: true,
          pointRadius: 3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#64748b',
            usePointStyle: true,
            padding: 16,
            font: { size: 11 },
          },
        },
        tooltip: {
          callbacks: {
            label: (ctx) =>
              `${ctx.dataset.label}: ₱${ctx.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`,
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#94a3b8',
            font: { size: 11 },
            callback: (v) => '₱' + Number(v).toLocaleString('en-PH'),
          },
          grid: { color: '#f1f5f9' },
        },
        x: {
          ticks: { color: '#94a3b8', font: { size: 11 } },
          grid:  { color: '#f1f5f9' },
        },
      },
    },
  });
};

// ── Export CSV ────────────────────────────────────────────────────────────────
const exportCSV = async () => {
  exporting.value = true;
  try {
    const params = new URLSearchParams({
      period: filters.period,
      ...(filters.date_from && { date_from: filters.date_from }),
      ...(filters.date_to   && { date_to:   filters.date_to   }),
    });

    const res = await fetch(`${API_URL}/admin/reports/export?${params}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const blob = await res.blob();
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `reports-${filters.period}-${Date.now()}.csv`;
    a.click();
    URL.revokeObjectURL(url);

  } catch (err) {
    console.error('Export failed:', err);
  } finally {
    exporting.value = false;
  }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const setPeriod = (period) => {
  filters.period = period;
  if (period !== 'custom') fetchReports();
};

const formatCurrency = (val) =>
  '₱' + Number(val ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const formatStatus = (status) =>
  (status ?? '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

onMounted(() => fetchReports());
onUnmounted(() => { if (chartInstance) chartInstance.destroy(); });
</script>

<style scoped>
/* ── Page ── */
.reports-page {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  padding: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  min-height: 100%;
}

/* ── Header ── */
.reports-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.1rem 1.5rem;
}

.header-brand { display: flex; align-items: center; gap: 0.875rem; }

.header-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #ecfdf5;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #059669;
  flex-shrink: 0;
}

.header-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.1rem;
  letter-spacing: -0.02em;
}

.header-sub { font-size: 0.75rem; color: #64748b; margin: 0; }

.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #fff;
  font-size: 0.78rem;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
}

.btn-export:hover:not(:disabled) { background: #f8fafc; }
.btn-export:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Filter Bar ── */
.filter-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1rem 1.5rem;
}

.period-pills { display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem; }
.period-label { font-size: 0.75rem; color: #94a3b8; margin-right: 0.25rem; }

.period-btn {
  padding: 0.3rem 0.875rem;
  border-radius: 999px;
  border: none;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.72rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}

.period-btn:hover { background: #e2e8f0; }
.period-btn--active { background: #059669; color: #fff; }

.custom-range { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

.date-input {
  padding: 0.35rem 0.65rem;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  font-size: 0.72rem;
  color: #374151;
  background: #f8fafc;
  font-family: inherit;
}

.date-input:focus { outline: none; border-color: #059669; }
.date-sep { color: #94a3b8; font-size: 0.75rem; }

.btn-apply {
  padding: 0.35rem 0.875rem;
  background: #059669;
  color: #fff;
  border: none;
  border-radius: 7px;
  font-size: 0.72rem;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
}

.btn-apply:hover { background: #047857; }

/* ── Summary Cards ── */
.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.summary-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.1rem 1.25rem;
}

.summary-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: #64748b;
  margin: 0 0 0.3rem;
}

.summary-amount {
  font-size: 1.55rem;
  font-weight: 800;
  color: #0f172a;
  text-align: right;
  margin: 0 0 0.4rem;
  letter-spacing: -0.03em;
}

.summary-hint {
  font-size: 0.68rem;
  font-weight: 500;
  text-align: right;
  margin: 0;
}

.summary-hint--blue   { color: #2563eb; }
.summary-hint--green  { color: #16a34a; }
.summary-hint--amber  { color: #d97706; }
.summary-hint--violet { color: #7c3aed; }

/* ── Chart Card ── */
.chart-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
}

.chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.card-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.15rem;
}

.card-sub { font-size: 0.72rem; color: #64748b; margin: 0; }

.chart-wrapper { height: 260px; position: relative; }

/* ── Insights ── */
.insights-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.insight-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
}

.insight-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.insight-tag {
  font-size: 0.68rem;
  color: #94a3b8;
}

.breakdown-list { display: flex; flex-direction: column; gap: 0.5rem; }

.breakdown-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #374151;
  padding: 0.25rem 0;
}

.breakdown-item--divider { border-top: 1px solid #f1f5f9; padding-top: 0.6rem; margin-top: 0.1rem; }
.breakdown-item--total   { font-weight: 600; color: #0f172a; }

.breakdown-value           { font-weight: 600; color: #0f172a; }
.breakdown-value--green    { color: #059669; }
.breakdown-value--amber    { color: #d97706; }

.stat-dot-row { display: flex; align-items: center; gap: 0.5rem; }

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot--gray  { background: #6b7280; }
.dot--amber { background: #f59e0b; }
.dot--green { background: #10b981; }

/* ── Table Card ── */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
}

.table-card-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.table-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  padding: 2.5rem;
  font-size: 0.82rem;
  color: #64748b;
}

.table-scroll { overflow-x: auto; }

.r-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 0.8rem;
  text-align: left;
}

.th {
  padding: 0.7rem 1.25rem;
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #64748b;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.th--center { text-align: center; }
.th--right  { text-align: right; }

.table-row {
  border-bottom: 1px solid #f8fafc;
  transition: background 0.15s;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover      { background: #f8fafc; }

.td {
  padding: 0.875rem 1.25rem;
  vertical-align: middle;
  color: #334155;
  overflow: hidden;
}

.td--center { text-align: center; }
.td--right  { text-align: right; }

.td--empty {
  padding: 2.5rem;
  text-align: center;
  color: #94a3b8;
  font-size: 0.8rem;
}

.job-id { font-weight: 700; color: #0f172a; font-variant-numeric: tabular-nums; }
.font-semibold { font-weight: 600; }
.text-green    { color: #16a34a; font-weight: 600; }
.text-amber    { color: #d97706; font-weight: 600; }

/* ── Status Badges ── */
.status-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.25rem 0.65rem;
  font-size: 0.68rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--completed    { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--in_progress  { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
.status-badge--scheduled    { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.status-badge--cancelled    { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }

/* ── Bottom Grid ── */
.bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

/* ── Top Revenue Jobs ── */
.top-jobs-list { display: flex; flex-direction: column; }

.top-job-item {
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto auto;
  column-gap: 0.75rem;
  row-gap: 0.5rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f8fafc;
}

.top-job-item:last-child { border-bottom: none; }

.top-job-left { display: flex; flex-direction: column; gap: 0.15rem; }
.top-job-title { font-weight: 600; font-size: 0.82rem; color: #0f172a; }
.top-job-id    { font-size: 0.68rem; color: #94a3b8; }

.top-job-footer {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.78rem;
  color: #475569;
}

.top-job-client { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.top-job-amount { font-weight: 700; color: #0f172a; flex-shrink: 0; }

.empty-msg {
  padding: 2.5rem;
  text-align: center;
  font-size: 0.82rem;
  color: #94a3b8;
  margin: 0;
}

/* ── Spinner ── */
.spinner { animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Responsive ── */
@media (max-width: 1100px) {
  .summary-grid  { grid-template-columns: repeat(2, 1fr); }
  .bottom-grid   { grid-template-columns: 1fr; }
  .insights-grid { grid-template-columns: 1fr; }
}

@media (max-width: 700px) {
  .summary-grid  { grid-template-columns: 1fr; }
  .reports-page  { padding: 1rem; }
  .filter-bar    { flex-direction: column; align-items: flex-start; }
  .custom-range  { flex-wrap: wrap; }
}
</style>