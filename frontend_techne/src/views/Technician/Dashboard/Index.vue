<!-- src/views/Technician/Dashboard/Index.vue -->
<template>
  <div class="dashboard">

    <!-- Header -->
    <div class="dashboard-header">
      <p class="breadcrumb">Dashboard</p>
      <div class="header-row">
        <h1 class="welcome-title">
          Welcome back, {{ user?.firstname ?? 'Technician' }}
        </h1>
        <span class="updated-badge">
          <i class="fas fa-clock"></i>
          Updated {{ formattedDate }}
        </span>
      </div>
      <p class="header-sub">A concise view of the work that needs your attention right now.</p>
    </div>

    <!-- Metrics -->
    <div class="metrics-grid">

      <div class="metric-card">
        <div class="metric-top">
          <p class="metric-label">Active job orders</p>
          <span class="metric-icon blue"><i class="fas fa-briefcase"></i></span>
        </div>
        <p class="metric-value">{{ metrics.active_jobs }}</p>
        <p class="metric-sub">Scheduled or currently in progress</p>
      </div>

      <div class="metric-card">
        <div class="metric-top">
          <p class="metric-label">Completed jobs</p>
          <span class="metric-icon green"><i class="fas fa-check-circle"></i></span>
        </div>
        <p class="metric-value">{{ metrics.completed_jobs }}</p>
        <p class="metric-sub">Marked as completed</p>
      </div>

      <div class="metric-card">
        <div class="metric-top">
          <p class="metric-label">Open inquiries</p>
          <span class="metric-icon yellow"><i class="fas fa-inbox"></i></span>
        </div>
        <p class="metric-value">{{ metrics.open_inquiries }}</p>
        <p class="metric-sub">Assigned to you and not closed</p>
      </div>

      <div class="metric-card">
        <div class="metric-top">
          <p class="metric-label">Quotations created</p>
          <span class="metric-icon purple"><i class="fas fa-file-invoice"></i></span>
        </div>
        <p class="metric-value">{{ metrics.quotations }}</p>
        <p class="metric-sub">Drafted or sent to managers</p>
      </div>

    </div>

    <!-- Two-column section -->
    <div class="two-col-grid">

      <!-- Job Order Focus -->
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="card-title">Job order focus</h2>
            <p class="card-sub">Most recent assignments</p>
          </div>
          <router-link to="/technician/job-orders" class="card-link">View all</router-link>
        </div>

        <div class="card-body">
          <template v-if="recentJobOrders.length">
            <div v-for="job in recentJobOrders" :key="job.id" class="list-item">
              <div class="item-info">
                <p class="item-id">JO-{{ padId(job.id) }}</p>
                <p class="item-title">{{ job.quotation?.project_title ?? 'No project title' }}</p>
                <p class="item-meta">Target: {{ formatDate(job.expected_finish_date) ?? 'Not set' }}</p>
              </div>
              <div class="item-actions">
                <span :class="['badge', jobStatusClass(job.status)]">
                  {{ (job.status ?? 'pending').replace('_', ' ') }}
                </span>
                <router-link :to="`/technician/job-orders/${job.id}`" class="card-link">
                  View details
                </router-link>
              </div>
            </div>
          </template>
          <div v-else class="empty-state">
            <div class="empty-icon"><i class="fas fa-briefcase"></i></div>
            <p class="empty-title">No job orders yet</p>
            <p class="empty-sub">Assigned job orders will show up here.</p>
          </div>
        </div>
      </div>

      <!-- Customer Inquiries -->
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="card-title">Customer inquiries</h2>
            <p class="card-sub">Latest items assigned to you</p>
          </div>
          <router-link to="/technician/inquiries" class="card-link">Manage</router-link>
        </div>

        <div class="card-body">
          <template v-if="recentInquiries.length">
            <div v-for="inquiry in recentInquiries" :key="inquiry.id" class="list-item">
              <div class="item-info">
                <p class="item-id">INQ-{{ padId(inquiry.id) }}</p>
                <p class="item-title">{{ inquiry.name ?? `Customer ${inquiry.customer_id}` }}</p>
                <p class="item-meta">{{ inquiry.category ?? 'General' }} • {{ formatDate(inquiry.created_at) }}</p>
              </div>
              <div class="item-actions">
                <span :class="['badge', inquiryStatusClass(inquiry.status)]">
                  {{ inquiry.status ?? 'new' }}
                </span>
                <router-link :to="`/technician/inquiries/${inquiry.id}`" class="card-link">
                  Open
                </router-link>
              </div>
            </div>
          </template>
          <div v-else class="empty-state">
            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
            <p class="empty-title">No inquiries assigned</p>
            <p class="empty-sub">New assignments will appear here.</p>
          </div>
        </div>
      </div>

    </div>

    <!-- Recent Quotations -->
    <div class="card">
      <div class="card-header">
        <div>
          <h2 class="card-title">Recent quotations</h2>
          <p class="card-sub">Quick glance at your latest submissions</p>
        </div>
        <router-link to="/technician/quotations" class="card-link">Go to quotations</router-link>
      </div>

      <div class="card-body">
        <template v-if="recentQuotations.length">
          <div v-for="quote in recentQuotations" :key="quote.id" class="list-item">
            <div class="item-info">
              <p class="item-id">QT-{{ padId(quote.id) }}</p>
              <p class="item-title">{{ quote.project_title ?? 'Untitled project' }}</p>
              <p class="item-meta">Issued {{ formatDate(quote.date_issued) ?? 'Not dated' }}</p>
            </div>
            <div class="item-actions">
              <span :class="['badge', quotationStatusClass(quote.status)]">
                {{ (quote.status ?? 'draft').toLowerCase() }}
              </span>
              <router-link :to="`/technician/quotations/${quote.id}`" class="card-link">
                Review
              </router-link>
            </div>
          </div>
        </template>
        <div v-else class="empty-state">
          <div class="empty-icon"><i class="fas fa-file-invoice"></i></div>
          <p class="empty-title">No quotations yet</p>
          <p class="empty-sub">Your created quotations will appear here.</p>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
  metrics: {
    type: Object,
    default: () => ({
      active_jobs: 0,
      completed_jobs: 0,
      open_inquiries: 0,
      quotations: 0,
    }),
  },
  recentJobOrders:  { type: Array, default: () => [] },
  recentInquiries:  { type: Array, default: () => [] },
  recentQuotations: { type: Array, default: () => [] },
});

const auth = useAuthStore();
const user = computed(() => auth.user);

const formattedDate = computed(() =>
  new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
);

const padId      = (id)   => String(id).padStart(5, '0');
const formatDate = (date) => {
  if (!date) return null;
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const jobStatusClass = (status) => ({
  scheduled:   'badge-blue',
  in_progress: 'badge-yellow',
  completed:   'badge-green',
  cancelled:   'badge-red',
}[status] ?? 'badge-gray');

const inquiryStatusClass = (status) => ({
  new:       'badge-blue',
  open:      'badge-yellow',
  responded: 'badge-green',
  closed:    'badge-gray',
}[status] ?? 'badge-gray');

const quotationStatusClass = (status) => ({
  approved: 'badge-green',
  pending:  'badge-yellow',
  rejected: 'badge-red',
}[status?.toLowerCase()] ?? 'badge-gray');
</script>

<style scoped>
/* ── Layout ──────────────────────────────────────────────────────────────── */
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.dashboard-header {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.breadcrumb {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.welcome-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.updated-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  background: #f3f4f6;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
}

.header-sub {
  font-size: 0.875rem;
  color: #4b5563;
  margin: 0;
}

/* ── Metrics Grid ────────────────────────────────────────────────────────── */
.metrics-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, 1fr);
}

@media (min-width: 1024px) {
  .metrics-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.metric-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.metric-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.metric-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.metric-icon {
  height: 2rem;
  width: 2rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  flex-shrink: 0;
}

.metric-icon.blue   { background: #eff6ff; color: #2563eb; }
.metric-icon.green  { background: #f0fdf4; color: #16a34a; }
.metric-icon.yellow { background: #fefce8; color: #a16207; }
.metric-icon.purple { background: #faf5ff; color: #7e22ce; }

.metric-value {
  font-size: 1.875rem;
  font-weight: 600;
  color: #111827;
  margin: 0.5rem 0 0;
}

.metric-sub {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0.25rem 0 0;
}

/* ── Two-column Grid ─────────────────────────────────────────────────────── */
.two-col-grid {
  display: grid;
  gap: 1.5rem;
}

@media (min-width: 1024px) {
  .two-col-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ── Card ────────────────────────────────────────────────────────────────── */
.card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.card-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.card-sub {
  font-size: 0.8rem;
  color: #6b7280;
  margin: 0.125rem 0 0;
}

.card-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: #2563eb;
  text-decoration: none;
  white-space: nowrap;
}

.card-link:hover {
  color: #1d4ed8;
}

.card-body > * + * {
  border-top: 1px solid #f3f4f6;
}

/* ── List Items ──────────────────────────────────────────────────────────── */
.list-item {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
}

.item-info {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.item-id {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.item-title {
  font-size: 0.875rem;
  color: #4b5563;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-meta {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0;
}

.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.375rem;
  flex-shrink: 0;
}

/* ── Badges ──────────────────────────────────────────────────────────────── */
.badge {
  padding: 0.2rem 0.6rem;
  font-size: 0.7rem;
  border-radius: 9999px;
  text-transform: capitalize;
  font-weight: 500;
  border: 1px solid transparent;
}

.badge-blue   { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.badge-green  { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.badge-yellow { background: #fef9c3; color: #854d0e; border-color: #fde68a; }
.badge-red    { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.badge-gray   { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }

/* ── Empty State ─────────────────────────────────────────────────────────── */
.empty-state {
  padding: 2.5rem 1.25rem;
  text-align: center;
}

.empty-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 2.5rem;
  width: 2.5rem;
  border-radius: 9999px;
  background: #f3f4f6;
  color: #6b7280;
  margin-bottom: 0.5rem;
  font-size: 1rem;
}

.empty-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #4b5563;
  margin: 0;
}

.empty-sub {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0.25rem 0 0;
}
</style>