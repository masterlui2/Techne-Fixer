<!-- src/views/admin/QuotationsView.vue -->
<template>
  <div class="quotations-page">

    <!-- ── Page Header ── -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Quotation Review</h1>
        <p class="page-subtitle">Review and approve technician quotations</p>
      </div>

      <div class="header-actions">
        <div class="search-wrapper">
          <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search quotations..."
            class="search-input"
            @input="debouncedFetch"
          />
        </div>

        <button class="btn-filter" @click="fetchQuotations(1)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Filter
        </button>
      </div>
    </div>

    <!-- ── Flash ── -->
    <transition name="slide-down">
      <div v-if="flash.message" class="flash" :class="`flash--${flash.type}`">
        {{ flash.message }}
      </div>
    </transition>

    <!-- ── Stat Cards ── -->
    <div class="stats-grid">
      <div class="stat-card">
        <p class="stat-label">Pending Review</p>
        <p class="stat-count stat-count--amber">{{ stats.pendingCount }}</p>
        <p class="stat-sub">Waiting for your approval</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Approved This Week</p>
        <p class="stat-count stat-count--emerald">{{ stats.approvedThisWeek }}</p>
        <p class="stat-sub">Ready for processing</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Pending Value</p>
        <p class="stat-count stat-count--blue">{{ formatCurrency(stats.pendingValue) }}</p>
        <p class="stat-sub">Total awaiting approval</p>
      </div>
    </div>

    <!-- ── Table ── -->
    <div class="table-card">

      <!-- Loading overlay -->
      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        <span>Loading quotations…</span>
      </div>

      <div class="table-scroll">
        <table class="q-table">
          <thead>
            <tr>
              <th class="th" style="width:10%">Quote #</th>
              <th class="th" style="width:25%">Customer & Project</th>
              <th class="th" style="width:15%">Technician</th>
              <th class="th th--right" style="width:13%">Amount</th>
              <th class="th th--right" style="width:12%">Submitted</th>
              <th class="th th--center" style="width:11%">Status</th>
              <th class="th th--center" style="width:14%">Actions</th>
            </tr>
          </thead>

          <tbody>
            <template v-if="quotations.length > 0">
              <tr
                v-for="quote in quotations"
                :key="quote.id"
                class="table-row"
              >
                <!-- Quote # -->
                <td class="td">
                  <span class="quote-id">Q-{{ String(quote.id).padStart(5, '0') }}</span>
                </td>

                <!-- Customer & Project -->
                <td class="td">
                  <div class="client-name">{{ quote.client_name }}</div>
                  <div class="project-title">{{ quote.project_title }}</div>
                </td>

                <!-- Technician -->
                <td class="td">
                  <span class="tech-name">{{ quote.technician?.name ?? '—' }}</span>
                </td>

                <!-- Amount -->
                <td class="td td--right">
                  <span class="amount">{{ formatCurrency(quote.grand_total) }}</span>
                </td>

                <!-- Submitted -->
                <td class="td td--right">
                  <span class="submitted">{{ timeAgo(quote.date_issued) }}</span>
                </td>

                <!-- Status -->
                <td class="td td--center">
                  <span class="status-badge" :class="`status-badge--${quote.status}`">
                    {{ capitalize(quote.status) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="td td--center">
                  <div class="action-row">
                    <button
                      v-if="quote.status === 'pending'"
                      class="btn-approve"
                      :disabled="approvingId === quote.id"
                      @click="approveQuotation(quote.id)"
                    >
                      <svg v-if="approvingId === quote.id" class="spinner spinner--sm" width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                      </svg>
                      <template v-else>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                          <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Downpayment paid
                      </template>
                    </button>

                    <span v-else class="action-none">—</span>
                  </div>
                </td>
              </tr>
            </template>

            <!-- Empty -->
            <tr v-else-if="!loading">
              <td colspan="7" class="td--empty">
                <div class="empty-state">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <div class="empty-title">No quotations to review</div>
                  <div class="empty-sub">All pending quotations have been processed.</div>
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
        >← Prev</button>

        <div class="page-numbers">
          <button
            v-for="page in visiblePages"
            :key="page"
            class="page-btn"
            :class="{ 'page-btn--active': page === pagination.current_page }"
            @click="goToPage(page)"
          >{{ page }}</button>
        </div>

        <button
          class="page-btn"
          :disabled="pagination.current_page === pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
        >Next →</button>

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

// ── State ─────────────────────────────────────────────────────────────────────
const quotations  = ref([]);
const loading     = ref(true);
const approvingId = ref(null);

const stats = ref({
  pendingCount:      0,
  approvedThisWeek:  0,
  pendingValue:      0,
});

const pagination = ref({
  current_page: 1,
  last_page:    1,
  total:        0,
});

const filters = reactive({ search: '' });

const flash = reactive({ message: '', type: 'success' });

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchQuotations = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page,
      ...(filters.search && { search: filters.search }),
    });

    const res = await fetch(`${API_URL}/admin/quotations?${params}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();

    quotations.value = data.data         ?? data.quotations?.data ?? [];
    pagination.value = {
      current_page: data.current_page    ?? data.quotations?.current_page ?? 1,
      last_page:    data.last_page       ?? data.quotations?.last_page    ?? 1,
      total:        data.total           ?? data.quotations?.total        ?? 0,
    };

    if (data.stats) {
      stats.value = {
        pendingCount:     data.stats.pending_count      ?? 0,
        approvedThisWeek: data.stats.approved_this_week ?? 0,
        pendingValue:     data.stats.pending_value      ?? 0,
      };
    }

  } catch (err) {
    console.error('Failed to fetch quotations:', err);
    showFlash('Failed to load quotations.', 'error');
  } finally {
    loading.value = false;
  }
};

// ── Approve ───────────────────────────────────────────────────────────────────
const approveQuotation = async (id) => {
  approvingId.value = id;
  try {
    const res = await fetch(`${API_URL}/admin/quotations/${id}/approve`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    showFlash('Quotation approved — downpayment confirmed.', 'success');
    await fetchQuotations(pagination.value.current_page);

  } catch (err) {
    console.error('Approve failed:', err);
    showFlash('Failed to approve quotation. Please try again.', 'error');
  } finally {
    approvingId.value = null;
  }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatCurrency = (val) => {
  if (val == null) return '₱0.00';
  return '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const timeAgo = (iso) => {
  if (!iso) return '—';
  const diff = Date.now() - new Date(iso).getTime();
  const mins  = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days  = Math.floor(diff / 86400000);
  if (mins < 1)   return 'just now';
  if (mins < 60)  return `${mins}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days < 7)   return `${days}d ago`;
  return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' });
};

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

const showFlash = (message, type = 'success') => {
  flash.message = message;
  flash.type    = type;
  setTimeout(() => { flash.message = ''; }, 3500);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchQuotations(page);
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

let searchTimer = null;
const debouncedFetch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchQuotations(1), 400);
};

onMounted(() => fetchQuotations());
</script>

<style scoped>
/* ── Page ── */
.quotations-page {
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
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
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

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

/* ── Search ── */
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
  width: 240px;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #94a3b8;
  box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
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

/* ── Flash ── */
.flash {
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.85rem;
}

.flash--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.flash--error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

/* ── Stats ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 14px;
  padding: 1.1rem 1.25rem;
}

.stat-label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #64748b;
  margin: 0 0 0.4rem;
}

.stat-count {
  font-size: 1.75rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin: 0 0 0.25rem;
}

.stat-count--amber   { color: #d97706; }
.stat-count--emerald { color: #059669; }
.stat-count--blue    { color: #2563eb; }

.stat-sub {
  font-size: 0.7rem;
  color: #94a3b8;
  margin: 0;
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

.q-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 0.8rem;
}

/* ── Head ── */
.th {
  padding: 0.75rem 1.25rem;
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #64748b;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
  text-align: left;
}

.th--right  { text-align: right; }
.th--center { text-align: center; }

/* ── Body ── */
.table-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f8fafc; }

.td {
  padding: 0.875rem 1.25rem;
  vertical-align: middle;
  color: #0f172a;
  overflow: hidden;
}

.td--right  { text-align: right; }
.td--center { text-align: center; }

/* ── Cell contents ── */
.quote-id {
  font-weight: 700;
  font-size: 0.8rem;
  font-variant-numeric: tabular-nums;
  color: #0f172a;
}

.client-name {
  font-weight: 600;
  font-size: 0.8rem;
  color: #0f172a;
  word-break: break-word;
}

.project-title {
  font-size: 0.72rem;
  color: #64748b;
  margin-top: 2px;
  word-break: break-word;
}

.tech-name {
  font-size: 0.78rem;
  color: #475569;
  word-break: break-word;
}

.amount {
  font-weight: 700;
  font-size: 0.82rem;
  color: #0f172a;
  white-space: nowrap;
}

.submitted {
  font-size: 0.75rem;
  color: #64748b;
  white-space: nowrap;
}

/* ── Status badges ── */
.status-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.28rem 0.7rem;
  font-size: 0.7rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge--pending  { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.status-badge--approved { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--rejected { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }

/* ── Actions ── */
.action-row {
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-approve {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.75rem;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #15803d;
  border-radius: 8px;
  font-size: 0.72rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-approve:hover:not(:disabled) {
  background: #dcfce7;
  border-color: #86efac;
  color: #166534;
}

.btn-approve:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-none {
  font-size: 0.75rem;
  color: #cbd5e1;
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

.empty-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #475569;
}

.empty-sub { font-size: 0.78rem; color: #94a3b8; }

/* ── Pagination ── */
.pagination {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.4rem;
  padding: 0.875rem 1.25rem;
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

.spinner--sm { color: #15803d; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-leave-to     { opacity: 0; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .stats-grid   { grid-template-columns: 1fr 1fr; }
  .page-header  { flex-direction: column; align-items: flex-start; }
  .header-actions { width: 100%; }
  .search-input { width: 100%; }
}

@media (max-width: 600px) {
  .stats-grid        { grid-template-columns: 1fr; }
  .quotations-page   { padding: 1rem; }
}
</style>