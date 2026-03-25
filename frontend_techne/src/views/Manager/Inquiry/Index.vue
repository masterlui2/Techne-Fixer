<!-- src/views/admin/InquiriesView.vue -->
<template>
  <div class="inquiries-page">

    <!-- ── Page Header ── -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Inquiries Management</h1>
        <p class="page-subtitle">Review customer inquiries and assign technicians for assessment.</p>
      </div>

      <!-- ── Filters ── -->
      <div class="filters-bar">
        <div class="search-wrapper">
          <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search customer, email, INQ-#, or description..."
            class="search-input"
            @input="debouncedFetch"
          />
        </div>

        <div class="select-wrapper">
          <select v-model="filters.status" class="filter-select" @change="fetchInquiries">
            <option value="">All Statuses</option>
            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
          </select>
          <span class="select-chevron">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
              <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </span>
        </div>

        <div class="select-wrapper">
          <select v-model="filters.technician" class="filter-select" @change="fetchInquiries">
            <option value="">All Technicians</option>
            <option v-for="t in technicians" :key="t.id" :value="t.id">
              {{ t.user?.firstname }} {{ t.user?.lastname }}
            </option>
          </select>
          <span class="select-chevron">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
              <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </span>
        </div>

        <button class="btn-filter" @click="fetchInquiries">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Filter
        </button>
      </div>
    </div>

    <!-- ── Flash Message ── -->
    <transition name="slide-down">
      <div v-if="flashMessage" class="flash-success">
        {{ flashMessage }}
      </div>
    </transition>

    <!-- ── 48h Warning ── -->
    <transition name="slide-down">
      <div v-if="unanswered > 0" class="warning-banner">
        ⚠️ {{ unanswered }} inquiries have been unattended for more than 48 hours — consider assigning a technician.
      </div>
    </transition>

    <!-- ── Stat Cards ── -->
    <div class="stats-grid">
      <div
        v-for="(count, key) in stats"
        :key="key"
        class="stat-card"
        :class="`stat-card--${key}`"
      >
        <p class="stat-label">{{ statLabels[key] ?? key }}</p>
        <p class="stat-count">{{ count }}</p>
        <p class="stat-sub">{{ statSubtitles[key] ?? '' }}</p>
      </div>
    </div>

    <!-- ── Table ── -->
    <div class="table-card">

      <!-- Loading overlay -->
      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        <span>Loading inquiries…</span>
      </div>

      <div class="table-scroll">
        <table class="inquiry-table">
          <thead>
            <tr>
              <th class="th th--center">Inquiry #</th>
              <th class="th">Customer</th>
              <th class="th">Issue</th>
              <th class="th th--center">Technician</th>
              <th class="th th--center">Status</th>
              <th class="th th--center">Created</th>
              <th class="th th--center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <template v-if="inquiries.length > 0">
              <tr
                v-for="inquiry in inquiries"
                :key="inquiry.id"
                class="table-row"
              >
                <!-- ID -->
                <td class="td td--center">
                  <span class="inquiry-id">INQ-{{ String(inquiry.id).padStart(5, '0') }}</span>
                </td>

                <!-- Customer -->
                <td class="td">
                  <div class="customer-name">{{ inquiry.name || customerName(inquiry) }}</div>
                  <div class="customer-meta">{{ inquiry.email }}</div>
                  <div class="customer-meta">{{ inquiry.contact_number }}</div>
                </td>

                <!-- Issue -->
                <td class="td td--issue">
                  <div class="issue-category">{{ inquiry.category }}</div>
                  <p class="issue-desc">{{ inquiry.issue_description }}</p>
                </td>

                <!-- Technician -->
                <td class="td td--center">
                  <span v-if="inquiry.technician" class="badge-technician">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                      <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    {{ inquiry.technician.user?.firstname }} {{ inquiry.technician.user?.lastname }}
                  </span>
                  <span v-else class="unassigned-label">Unassigned</span>
                </td>

                <!-- Status -->
                <td class="td td--center">
                  <span class="status-badge" :class="`status-badge--${slugify(inquiry.status)}`">
                    {{ inquiry.status ?? 'Pending' }}
                  </span>
                </td>

                <!-- Created -->
                <td class="td td--center">
                  <div class="date-main">{{ formatDate(inquiry.created_at) }}</div>
                  <div class="date-time">{{ formatTime(inquiry.created_at) }}</div>
                </td>

                <!-- Actions -->
                <td class="td td--center">
                  <div class="action-row">
                    <div class="select-wrapper select-wrapper--sm">
                      <select
                        v-model="assignSelections[inquiry.id]"
                        class="filter-select filter-select--sm"
                      >
                        <option value="">Assign…</option>
                        <option v-for="t in technicians" :key="t.id" :value="t.id">
                          {{ t.user?.firstname }} {{ t.user?.lastname }}
                        </option>
                      </select>
                      <span class="select-chevron">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                          <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                      </span>
                    </div>
                    <button
                      class="btn-assign"
                      :disabled="!assignSelections[inquiry.id] || assigningId === inquiry.id"
                      @click="assignTechnician(inquiry.id)"
                    >
                      <svg v-if="assigningId === inquiry.id" class="spinner spinner--sm" width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                      </svg>
                      <span v-else>Assign</span>
                    </button>
                  </div>
                </td>
              </tr>
            </template>

            <!-- Empty state -->
            <tr v-else-if="!loading">
              <td colspan="7" class="td--empty">
                <div class="empty-state">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <div class="empty-title">No inquiries to manage</div>
                  <div class="empty-sub">All current inquiries have been assigned and processed.</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ── Pagination ── -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button
          class="page-btn"
          :disabled="pagination.current_page === 1"
          @click="goToPage(pagination.current_page - 1)"
        >
          ← Prev
        </button>

        <div class="page-numbers">
          <button
            v-for="page in visiblePages"
            :key="page"
            class="page-btn"
            :class="{ 'page-btn--active': page === pagination.current_page }"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </div>

        <button
          class="page-btn"
          :disabled="pagination.current_page === pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
        >
          Next →
        </button>

        <span class="page-info">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── Data ──────────────────────────────────────────────────────────────────────
const inquiries    = ref([]);
const technicians  = ref([]);
const loading      = ref(true);
const flashMessage = ref('');
const unanswered   = ref(0);
const assigningId  = ref(null);

const stats = ref({
  unassigned: 0,
  pending:    0,
  assigned:   0,
  ongoing:    0,
  completed:  0,
  cancelled:  0,
  scheduled:  0,
  converted:  0,
});

const pagination = ref({
  current_page: 1,
  last_page:    1,
  total:        0,
});

const filters = reactive({
  search:     '',
  status:     '',
  technician: '',
});

// Key → selected technician id for each row
const assignSelections = reactive({});

// ── Constants ─────────────────────────────────────────────────────────────────
const statuses = ['Pending', 'Acknowledged', 'In Progress', 'Scheduled', 'Completed', 'Cancelled', 'Converted'];

const statLabels = {
  unassigned: 'Unassigned', pending: 'Pending', assigned: 'Assigned',
  ongoing: 'Ongoing', completed: 'Completed', cancelled: 'Cancelled',
  scheduled: 'Scheduled', converted: 'Converted',
};

const statSubtitles = {
  unassigned: 'Need technician assignment',
  pending:    'Awaiting review',
  assigned:   'Under assessment',
  ongoing:    'Technician working',
  completed:  'Work finished',
  cancelled:  'Inquiry closed',
  scheduled:  'For onsite visit',
  converted:  'Turned into quotations',
};

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchInquiries = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page,
      ...(filters.search     && { search:     filters.search }),
      ...(filters.status     && { status:     filters.status }),
      ...(filters.technician && { technician: filters.technician }),
    });

    const [inquiryRes, techRes] = await Promise.all([
      fetch(`${API_URL}/admin/inquiries?${params}`),
      technicians.value.length ? Promise.resolve(null) : fetch(`${API_URL}/admin/technicians`),
    ]);

    if (!inquiryRes.ok) throw new Error(`HTTP ${inquiryRes.status}`);
    const data = await inquiryRes.json();

    inquiries.value   = data.data ?? data.inquiries?.data ?? [];
    pagination.value  = {
      current_page: data.current_page ?? data.inquiries?.current_page ?? 1,
      last_page:    data.last_page    ?? data.inquiries?.last_page    ?? 1,
      total:        data.total        ?? data.inquiries?.total        ?? 0,
    };

    if (data.stats)      stats.value     = data.stats;
    if (data.unanswered) unanswered.value = data.unanswered;

    if (techRes) {
      const techData = await techRes.json();
      technicians.value = techData.data ?? techData;
    }

    // Pre-fill assign selections with current technician
    inquiries.value.forEach(inq => {
      if (!(inq.id in assignSelections)) {
        assignSelections[inq.id] = inq.assigned_technician_id ?? '';
      }
    });

  } catch (err) {
    console.error('Failed to fetch inquiries:', err);
  } finally {
    loading.value = false;
  }
};

// ── Assign ────────────────────────────────────────────────────────────────────
const assignTechnician = async (inquiryId) => {
  const technicianId = assignSelections[inquiryId];
  if (!technicianId) return;

  assigningId.value = inquiryId;
  try {
    const res = await fetch(`${API_URL}/admin/inquiries/${inquiryId}/assign`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ technician_id: technicianId }),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    showFlash('Technician assigned successfully.');
    await fetchInquiries(pagination.value.current_page);
  } catch (err) {
    console.error('Assign failed:', err);
    showFlash('Failed to assign technician. Please try again.', true);
  } finally {
    assigningId.value = null;
  }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const customerName = (inq) => {
  const first = inq.customer?.firstname ?? '';
  const last  = inq.customer?.lastname  ?? '';
  return (first + ' ' + last).trim() || 'Customer';
};

const formatDate = (iso) => {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' });
};

const formatTime = (iso) => {
  if (!iso) return '';
  return new Date(iso).toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
};

const slugify = (str) => (str ?? '').toLowerCase().replace(/\s+/g, '-');

const showFlash = (msg) => {
  flashMessage.value = msg;
  setTimeout(() => { flashMessage.value = ''; }, 3500);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchInquiries(page);
};

const visiblePages = computed(() => {
  const total   = pagination.value.last_page;
  const current = pagination.value.current_page;
  const delta   = 2;
  const pages   = [];
  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
    pages.push(i);
  }
  return pages;
});

// Debounce search
let searchTimer = null;
const debouncedFetch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchInquiries(1), 400);
};

onMounted(() => fetchInquiries());
</script>

<style scoped>
/* ── Page ── */
.inquiries-page {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  min-height: 100%;
}

/* ── Header ── */
.page-header {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.25rem;
}

.page-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.2rem;
  letter-spacing: -0.02em;
}

.page-subtitle {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

/* ── Filters ── */
.filters-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.search-wrapper {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 0.6rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.search-input {
  padding: 0.5rem 0.75rem 0.5rem 2rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.75rem;
  color: #0f172a;
  background: #fff;
  width: 260px;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #94a3b8;
  box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
}

.select-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.select-wrapper--sm { flex-shrink: 0; }

.select-chevron {
  pointer-events: none;
  position: absolute;
  right: 0.6rem;
  color: #64748b;
  display: flex;
  align-items: center;
}

.filter-select {
  appearance: none;
  padding: 0.5rem 2rem 0.5rem 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.75rem;
  color: #374151;
  background: #fff;
  font-family: inherit;
  cursor: pointer;
  transition: border-color 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #94a3b8;
}

.filter-select--sm {
  width: 138px;
  font-size: 0.72rem;
  padding: 0.35rem 1.75rem 0.35rem 0.6rem;
}

.btn-filter {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 0.875rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #fff;
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
}

.btn-filter:hover { background: #f8fafc; }

/* ── Flash / Warning ── */
.flash-success {
  border: 1px solid #a7f3d0;
  background: #ecfdf5;
  color: #065f46;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.85rem;
}

.warning-banner {
  border: 1px solid #fde68a;
  background: #fffbeb;
  color: #92400e;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.85rem;
}

/* ── Stat Cards ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 14px;
  padding: 1rem 1.25rem;
}

.stat-label {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #64748b;
  margin: 0 0 0.5rem;
}

.stat-count {
  font-size: 1.75rem;
  font-weight: 800;
  margin: 0 0 0.25rem;
  letter-spacing: -0.03em;
}

.stat-sub {
  font-size: 0.7rem;
  color: #94a3b8;
  margin: 0;
}

/* Stat color accents */
.stat-card--unassigned .stat-count { color: #d97706; }
.stat-card--pending    .stat-count { color: #4f46e5; }
.stat-card--assigned   .stat-count { color: #2563eb; }
.stat-card--ongoing    .stat-count { color: #7c3aed; }
.stat-card--completed  .stat-count { color: #059669; }
.stat-card--cancelled  .stat-count { color: #e11d48; }
.stat-card--scheduled  .stat-count { color: #475569; }
.stat-card--converted  .stat-count { color: #16a34a; }

/* ── Table Card ── */
.table-card {
  position: relative;
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
}

.table-loading {
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.8);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  z-index: 10;
  font-size: 0.85rem;
  color: #64748b;
  border-radius: 14px;
}

.table-scroll { overflow-x: auto; }

.inquiry-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8rem;
  text-align: left;
}

/* ── Table Head ── */
.th {
  padding: 0.75rem 1rem;
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

/* ── Table Body ── */
.table-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f8fafc; }

.td {
  padding: 0.875rem 1rem;
  vertical-align: middle;
  color: #0f172a;
}

.td--center { text-align: center; }

.td--issue { max-width: 280px; }

/* ── Cell Contents ── */
.inquiry-id {
  font-weight: 700;
  font-size: 0.8rem;
  color: #0f172a;
  font-variant-numeric: tabular-nums;
}

.customer-name {
  font-weight: 600;
  font-size: 0.8rem;
  color: #0f172a;
}

.customer-meta {
  font-size: 0.7rem;
  color: #64748b;
  margin-top: 1px;
}

.issue-category {
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #64748b;
  margin-bottom: 0.25rem;
}

.issue-desc {
  font-size: 0.75rem;
  color: #334155;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin: 0;
}

.badge-technician {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  border-radius: 999px;
  padding: 0.3rem 0.7rem;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.unassigned-label {
  font-size: 0.72rem;
  color: #94a3b8;
}

.date-main {
  font-size: 0.75rem;
  color: #0f172a;
  font-weight: 500;
}

.date-time {
  font-size: 0.68rem;
  color: #64748b;
  margin-top: 1px;
}

/* ── Status Badges ── */
.status-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.3rem 0.75rem;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--pending      { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-badge--acknowledged { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.status-badge--in-progress  { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
.status-badge--scheduled    { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.status-badge--completed    { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--cancelled    { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
.status-badge--converted    { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

/* ── Actions ── */
.action-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
}

.btn-assign {
  padding: 0.35rem 0.75rem;
  background: #0f172a;
  color: #fff;
  border: none;
  border-radius: 7px;
  font-size: 0.72rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  white-space: nowrap;
}

.btn-assign:hover:not(:disabled) { background: #1e293b; }

.btn-assign:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* ── Empty ── */
.td--empty {
  padding: 3rem 1rem;
  text-align: center;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  color: #94a3b8;
}

.empty-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #475569;
}

.empty-sub {
  font-size: 0.78rem;
  color: #94a3b8;
}

/* ── Pagination ── */
.pagination {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.4rem;
  padding: 0.875rem 1rem;
  border-top: 1px solid #f1f5f9;
  background: #f8fafc;
  flex-wrap: wrap;
}

.page-numbers { display: flex; gap: 0.4rem; }

.page-btn {
  padding: 0.35rem 0.7rem;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  background: #fff;
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.15s;
}

.page-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.page-btn--active {
  background: #0f172a;
  color: #fff;
  border-color: #0f172a;
}

.page-info {
  font-size: 0.72rem;
  color: #64748b;
  margin-left: 0.25rem;
}

/* ── Spinner ── */
.spinner {
  animation: spin 0.8s linear infinite;
  color: #64748b;
}

.spinner--sm { color: #fff; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-leave-to     { opacity: 0; }

/* ── Responsive ── */
@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 900px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .page-header { flex-direction: column; }
  .filters-bar { width: 100%; }
  .search-input { width: 100%; }
}

@media (max-width: 600px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .inquiries-page { padding: 1rem; }
}
</style>