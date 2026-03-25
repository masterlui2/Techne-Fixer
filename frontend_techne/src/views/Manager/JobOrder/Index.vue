<!-- src/views/admin/JobOrdersView.vue -->
<template>
  <div class="jo-page">

    <!-- ── Company Header ── -->
    <div class="company-header">
      <div class="company-info">
        <h1 class="company-name">Techne Fixer Computer and Laptop Repair Services</h1>
        <p class="company-detail">007 Manga Street, Toril Davao City</p>
        <p class="company-detail">Contact No: 09662406825 &nbsp;·&nbsp; TIN 618‑863‑736‑000000</p>
      </div>
      <img :src="logo" alt="Techne Fixer Logo" class="company-logo" />
    </div>

    <!-- ── Flash ── -->
    <transition name="slide-down">
      <div v-if="flash.message" class="flash" :class="`flash--${flash.type}`">
        {{ flash.message }}
      </div>
    </transition>

    <!-- ── Section Header + Filters ── -->
    <div class="section-header">
      <h2 class="section-title">Job Order Management</h2>

      <div class="filters-bar">
        <div class="search-wrapper">
          <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search by client or JO ID"
            class="search-input"
            @input="debouncedFetch"
          />
        </div>

        <div class="select-wrapper">
          <select v-model="filters.status" class="filter-select" @change="fetchJobOrders(1)">
            <option value="">All Status</option>
            <option value="scheduled">Scheduled</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <span class="select-chevron">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
              <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </span>
        </div>
      </div>
    </div>

    <!-- ── Table Card ── -->
    <div class="table-card">

      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        <span>Loading job orders…</span>
      </div>

      <div class="table-scroll">
        <table class="jo-table">
          <thead>
            <tr>
              <th class="th th--center">Job #</th>
              <th class="th">Client</th>
              <th class="th">Project Title</th>
              <th class="th th--center">Technician</th>
              <th class="th th--center">Start Date</th>
              <th class="th th--center">Target Completion</th>
              <th class="th th--center">Status</th>
              <th class="th th--center" style="width:220px">Actions</th>
            </tr>
          </thead>

          <tbody>
            <template v-if="jobOrders.length > 0">
              <tr v-for="job in jobOrders" :key="job.id" class="table-row">

                <!-- Job # -->
                <td class="td td--center">
                  <span class="job-id">JO-{{ String(job.id).padStart(5, '0') }}</span>
                </td>

                <!-- Client -->
                <td class="td">
                  <div class="client-name">{{ job.quotation?.client_name ?? 'N/A' }}</div>
                </td>

                <!-- Project Title -->
                <td class="td">
                  <div class="project-title">{{ job.quotation?.project_title ?? 'N/A' }}</div>
                </td>

                <!-- Technician -->
                <td class="td td--center">
                  <span v-if="job.technician" class="badge-technician">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                      <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    {{ job.technician.user?.firstname }} {{ job.technician.user?.lastname }}
                  </span>
                  <span v-else class="unassigned">Unassigned</span>
                </td>

                <!-- Start Date -->
                <td class="td td--center">
                  <div class="date-main">{{ formatDate(job.created_at) }}</div>
                  <div class="date-time">{{ formatTime(job.created_at) }}</div>
                </td>

                <!-- Target Completion -->
                <td class="td td--center">
                  <template v-if="job.expected_finish_date">
                    <div class="date-main">{{ formatDate(job.expected_finish_date) }}</div>
                    <div class="date-time">{{ formatTime(job.expected_finish_date) }}</div>
                  </template>
                  <span v-else class="not-set">Not set</span>
                </td>

                <!-- Status -->
                <td class="td td--center">
                  <span class="status-badge" :class="`status-badge--${job.status}`">
                    {{ formatStatus(job.status) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="td td--center">
                  <div class="action-row">
                    <div class="select-wrapper">
                      <select
                        v-model="assignSelections[job.id]"
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
                      :disabled="!assignSelections[job.id] || assigningId === job.id"
                      @click="assignTechnician(job.id)"
                    >
                      <svg v-if="assigningId === job.id" class="spinner spinner--sm" width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                      </svg>
                      <span v-else>Assign</span>
                    </button>

                    <router-link
                      :to="{ name: 'admin.job-orders.show', params: { id: job.id } }"
                      class="btn-view"
                      title="View"
                    >
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </router-link>
                  </div>
                </td>

              </tr>
            </template>

            <!-- Empty -->
            <tr v-else-if="!loading">
              <td colspan="8" class="td--empty">
                <div class="empty-state">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2M12 12v4M10 14h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <div class="empty-title">No job orders found</div>
                  <div class="empty-sub">Job orders will appear here once quotations are approved.</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ── Summary + Pagination ── -->
      <div class="table-footer">
        <div class="summary">
          <span>Total Job Orders: <strong class="text-blue">{{ pagination.total }}</strong></span>
          <span>Currently Active: <strong class="text-yellow">{{ activeCount }}</strong></span>
        </div>

        <div v-if="pagination.last_page > 1" class="pagination">
          <span class="page-info">
            Showing {{ showingFrom }}–{{ showingTo }} of {{ pagination.total }}
          </span>

          <button
            class="page-btn"
            :disabled="pagination.current_page === 1"
            @click="goToPage(pagination.current_page - 1)"
          >Prev</button>

          <button
            v-for="page in visiblePages"
            :key="page"
            class="page-btn"
            :class="{ 'page-btn--active': page === pagination.current_page }"
            @click="goToPage(page)"
          >{{ page }}</button>

          <button
            class="page-btn"
            :disabled="pagination.current_page === pagination.last_page"
            @click="goToPage(pagination.current_page + 1)"
          >Next</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import logo from '@/assets/images/logo.png';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── State ─────────────────────────────────────────────────────────────────────
const jobOrders   = ref([]);
const technicians = ref([]);
const loading     = ref(true);
const assigningId = ref(null);
const activeCount = ref(0);

const pagination = ref({
  current_page: 1,
  last_page:    1,
  total:        0,
  per_page:     15,
});

const filters = reactive({ search: '', status: '' });
const assignSelections = reactive({});
const flash = reactive({ message: '', type: 'success' });

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchJobOrders = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page,
      ...(filters.search && { search: filters.search }),
      ...(filters.status && { status: filters.status }),
    });

    const [joRes, techRes] = await Promise.all([
      fetch(`${API_URL}/admin/job-orders?${params}`),
      technicians.value.length ? Promise.resolve(null) : fetch(`${API_URL}/admin/technicians`),
    ]);

    if (!joRes.ok) throw new Error(`HTTP ${joRes.status}`);
    const data = await joRes.json();

    jobOrders.value = data.data          ?? data.job_orders?.data ?? [];
    pagination.value = {
      current_page: data.current_page    ?? 1,
      last_page:    data.last_page       ?? 1,
      total:        data.total           ?? 0,
      per_page:     data.per_page        ?? 15,
    };

    activeCount.value = data.active_count ?? jobOrders.value.filter(j => j.status === 'in_progress').length;

    if (techRes) {
      const techData = await techRes.json();
      technicians.value = techData.data ?? techData;
    }

    // Pre-fill assign selections
    jobOrders.value.forEach(job => {
      if (!(job.id in assignSelections)) {
        assignSelections[job.id] = job.technician_id ?? '';
      }
    });

  } catch (err) {
    console.error('Failed to fetch job orders:', err);
    showFlash('Failed to load job orders.', 'error');
  } finally {
    loading.value = false;
  }
};

// ── Assign ────────────────────────────────────────────────────────────────────
const assignTechnician = async (jobId) => {
  const technicianId = assignSelections[jobId];
  if (!technicianId) return;

  assigningId.value = jobId;
  try {
    const res = await fetch(`${API_URL}/admin/job-orders/${jobId}/assign`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ technician_id: technicianId }),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    showFlash('Technician assigned successfully.', 'success');
    await fetchJobOrders(pagination.value.current_page);

  } catch (err) {
    console.error('Assign failed:', err);
    showFlash('Failed to assign technician. Please try again.', 'error');
  } finally {
    assigningId.value = null;
  }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatDate = (iso) => {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' });
};

const formatTime = (iso) => {
  if (!iso) return '';
  return new Date(iso).toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
};

const formatStatus = (status) =>
  (status ?? '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

const showFlash = (message, type = 'success') => {
  flash.message = message;
  flash.type    = type;
  setTimeout(() => { flash.message = ''; }, 3500);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchJobOrders(page);
};

const visiblePages = computed(() => {
  const total   = pagination.value.last_page;
  const current = pagination.value.current_page;
  const pages   = [];
  for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
    pages.push(i);
  }
  return pages;
});

const showingFrom = computed(() => {
  const { current_page, per_page, total } = pagination.value;
  if (total === 0) return 0;
  return (current_page - 1) * per_page + 1;
});

const showingTo = computed(() =>
  Math.min(pagination.value.current_page * pagination.value.per_page, pagination.value.total)
);

let searchTimer = null;
const debouncedFetch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchJobOrders(1), 400);
};

onMounted(() => fetchJobOrders());
</script>

<style scoped>
/* ── Page ── */
.jo-page {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1.5rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  min-height: 100%;
}

/* ── Company Header ── */
.company-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #e2e8f0;
}

.company-name {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.3rem;
}

.company-detail {
  font-size: 0.78rem;
  color: #64748b;
  margin: 0;
}

.company-logo {
  width: 64px;
  height: 64px;
  object-fit: contain;
  flex-shrink: 0;
}

/* ── Flash ── */
.flash {
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.85rem;
}

.flash--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.flash--error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

/* ── Section Header ── */
.section-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.01em;
}

/* ── Filters ── */
.filters-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.search-wrapper { position: relative; }

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
  width: 210px;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #94a3b8;
  box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
}

.select-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
}

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

.filter-select:focus { outline: none; border-color: #94a3b8; }

.filter-select--sm {
  width: 136px;
  font-size: 0.72rem;
  padding: 0.35rem 1.75rem 0.35rem 0.6rem;
}

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
  background: rgba(255,255,255,0.85);
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

.jo-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8rem;
  text-align: left;
}

/* ── Head ── */
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

/* ── Body ── */
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

/* ── Cell Contents ── */
.job-id {
  font-weight: 700;
  font-size: 0.8rem;
  font-variant-numeric: tabular-nums;
}

.client-name {
  font-weight: 600;
  font-size: 0.8rem;
  color: #0f172a;
}

.project-title {
  font-size: 0.78rem;
  color: #475569;
}

.badge-technician {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  border-radius: 999px;
  padding: 0.28rem 0.65rem;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.unassigned {
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

.not-set {
  font-size: 0.72rem;
  color: #94a3b8;
}

/* ── Status Badges ── */
.status-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.28rem 0.7rem;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--scheduled    { background: #dbeafe; color: #1d4ed8;  border: 1px solid #bfdbfe; }
.status-badge--in_progress  { background: #fef9c3; color: #854d0e;  border: 1px solid #fde68a; }
.status-badge--completed    { background: #d1fae5; color: #065f46;  border: 1px solid #a7f3d0; }
.status-badge--cancelled    { background: #ffe4e6; color: #be123c;  border: 1px solid #fecdd3; }

/* ── Actions ── */
.action-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
}

.btn-assign {
  padding: 0.35rem 0.65rem;
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
.btn-assign:disabled { opacity: 0.4; cursor: not-allowed; }

.btn-view {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 7px;
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  text-decoration: none;
  transition: all 0.2s;
  flex-shrink: 0;
}

.btn-view:hover {
  background: #dbeafe;
  color: #1d4ed8;
}

/* ── Empty ── */
.td--empty { padding: 3.5rem 1rem; text-align: center; }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  color: #94a3b8;
}

.empty-title { font-size: 0.9rem; font-weight: 600; color: #475569; }
.empty-sub   { font-size: 0.78rem; color: #94a3b8; }

/* ── Footer ── */
.table-footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  border-top: 1px solid #f1f5f9;
  background: #f8fafc;
}

.summary {
  display: flex;
  gap: 1.5rem;
  font-size: 0.78rem;
  color: #475569;
}

.text-blue   { color: #2563eb; font-weight: 700; }
.text-yellow { color: #d97706; font-weight: 700; }

/* ── Pagination ── */
.pagination {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.page-info {
  font-size: 0.72rem;
  color: #64748b;
  margin-right: 0.25rem;
}

.page-btn {
  padding: 0.3rem 0.65rem;
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

.page-btn:hover:not(:disabled) { background: #f1f5f9; border-color: #cbd5e1; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-btn--active  { background: #0f172a; color: #fff; border-color: #0f172a; }

/* ── Spinner ── */
.spinner     { animation: spin 0.8s linear infinite; color: #64748b; }
.spinner--sm { color: #fff; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-leave-to     { opacity: 0; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .section-header { flex-direction: column; align-items: flex-start; }
  .filters-bar    { width: 100%; }
  .search-input   { width: 100%; }
}

@media (max-width: 600px) {
  .jo-page { padding: 1rem; }
  .summary { flex-direction: column; gap: 0.25rem; }
}
</style>