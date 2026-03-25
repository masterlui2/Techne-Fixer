<!-- src/views/admin/CustomersView.vue -->
<template>
  <div class="customers-page">

    <!-- ── Page Header ── -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Customers</h1>
        <p class="page-subtitle">View customer profiles, inquiry history, and job records.</p>
      </div>

      <div class="header-actions">
        <span class="pill pill--blue">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          {{ pagination.total }} customers
        </span>
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
        <div class="stat-icon stat-icon--blue">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <div>
          <p class="stat-label">Total Customers</p>
          <p class="stat-count">{{ stats.total }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon--green">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M22 4L12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <p class="stat-label">New This Month</p>
          <p class="stat-count">{{ stats.newThisMonth }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon--amber">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <p class="stat-label">With Active Inquiries</p>
          <p class="stat-count">{{ stats.withActiveInquiries }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon--violet">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 7V5a2 2 0 00-4 0v2M8 7V5a2 2 0 00-4 0v2" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <div>
          <p class="stat-label">Completed Jobs</p>
          <p class="stat-count">{{ stats.completedJobs }}</p>
        </div>
      </div>
    </div>

    <!-- ── Filters ── -->
    <div class="filters-row">
      <div class="search-wrapper">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
          <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Search by name, email, or phone…"
          class="search-input"
          @input="debouncedFetch"
        />
      </div>

      <div class="select-wrapper">
        <select v-model="filters.sort" class="filter-select" @change="fetchCustomers(1)">
          <option value="newest">Newest first</option>
          <option value="oldest">Oldest first</option>
          <option value="name_asc">Name A–Z</option>
          <option value="name_desc">Name Z–A</option>
          <option value="most_inquiries">Most inquiries</option>
        </select>
        <span class="select-chevron">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
      </div>
    </div>

    <!-- ── Table ── -->
    <div class="table-card">

      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        <span>Loading customers…</span>
      </div>

      <div class="table-scroll">
        <table class="c-table">
          <thead>
            <tr>
              <th class="th">Customer</th>
              <th class="th">Contact</th>
              <th class="th th--center">Inquiries</th>
              <th class="th th--center">Job Orders</th>
              <th class="th th--center">Last Activity</th>
              <th class="th th--center">Status</th>
              <th class="th th--center" style="width:100px">Actions</th>
            </tr>
          </thead>

          <tbody>
            <template v-if="customers.length > 0">
              <tr v-for="customer in customers" :key="customer.id" class="table-row">

                <!-- Customer -->
                <td class="td">
                  <div class="customer-profile">
                    <div class="customer-avatar" :style="{ background: avatarColor(customer) }">
                      {{ initials(customer) }}
                    </div>
                    <div>
                      <div class="customer-name">
                        {{ customer.firstname }} {{ customer.lastname }}
                      </div>
                      <div class="customer-since">
                        Member since {{ formatDate(customer.created_at) }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Contact -->
                <td class="td">
                  <div class="contact-email">{{ customer.email }}</div>
                  <div class="contact-phone">{{ customer.phone ?? '—' }}</div>
                </td>

                <!-- Inquiries -->
                <td class="td td--center">
                  <span class="count-badge count-badge--blue">
                    {{ customer.inquiries_count ?? 0 }}
                  </span>
                </td>

                <!-- Job Orders -->
                <td class="td td--center">
                  <span class="count-badge count-badge--green">
                    {{ customer.job_orders_count ?? 0 }}
                  </span>
                </td>

                <!-- Last Activity -->
                <td class="td td--center">
                  <template v-if="customer.last_activity">
                    <div class="date-main">{{ formatDate(customer.last_activity) }}</div>
                    <div class="date-sub">{{ timeAgo(customer.last_activity) }}</div>
                  </template>
                  <span v-else class="no-activity">No activity</span>
                </td>

                <!-- Status -->
                <td class="td td--center">
                  <span class="status-badge" :class="customerStatus(customer)">
                    {{ customerStatusLabel(customer) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="td td--center">
                  <button
                    class="btn-view"
                    @click="openProfile(customer)"
                    title="View profile"
                  >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                      <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    View
                  </button>
                </td>

              </tr>
            </template>

            <tr v-else-if="!loading">
              <td colspan="7" class="td--empty">
                <div class="empty-state">
                  <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <div class="empty-title">No customers found</div>
                  <div class="empty-sub">Customers will appear here once they register or submit an inquiry.</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <span class="page-info">
          Showing {{ showingFrom }}–{{ showingTo }} of {{ pagination.total }}
        </span>

        <button class="page-btn" :disabled="pagination.current_page === 1" @click="goToPage(pagination.current_page - 1)">← Prev</button>

        <div class="page-numbers">
          <button
            v-for="page in visiblePages"
            :key="page"
            class="page-btn"
            :class="{ 'page-btn--active': page === pagination.current_page }"
            @click="goToPage(page)"
          >{{ page }}</button>
        </div>

        <button class="page-btn" :disabled="pagination.current_page === pagination.last_page" @click="goToPage(pagination.current_page + 1)">Next →</button>
      </div>

    </div>

    <!-- ── Customer Profile Drawer ── -->
    <transition name="drawer-slide">
      <div v-if="showProfile" class="drawer-backdrop" @click.self="closeProfile">
        <div class="drawer">

          <div class="drawer-header">
            <h3 class="drawer-title">Customer Profile</h3>
            <button class="modal-close" @click="closeProfile">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>

          <div v-if="profileCustomer" class="drawer-body">

            <!-- Profile Hero -->
            <div class="profile-hero">
              <div class="profile-avatar-lg" :style="{ background: avatarColor(profileCustomer) }">
                {{ initials(profileCustomer) }}
              </div>
              <div class="profile-hero-info">
                <h4 class="profile-fullname">{{ profileCustomer.firstname }} {{ profileCustomer.lastname }}</h4>
                <p class="profile-email">{{ profileCustomer.email }}</p>
                <p class="profile-phone">{{ profileCustomer.phone ?? 'No phone on record' }}</p>
              </div>
            </div>

            <!-- Stats Row -->
            <div class="profile-stats">
              <div class="profile-stat">
                <span class="profile-stat-count">{{ profileCustomer.inquiries_count ?? 0 }}</span>
                <span class="profile-stat-label">Inquiries</span>
              </div>
              <div class="profile-stat">
                <span class="profile-stat-count">{{ profileCustomer.job_orders_count ?? 0 }}</span>
                <span class="profile-stat-label">Job Orders</span>
              </div>
              <div class="profile-stat">
                <span class="profile-stat-count">{{ profileCustomer.completed_jobs ?? 0 }}</span>
                <span class="profile-stat-label">Completed</span>
              </div>
            </div>

            <!-- Info Grid -->
            <div class="profile-section">
              <h5 class="profile-section-title">Details</h5>
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">Member Since</span>
                  <span class="info-value">{{ formatDate(profileCustomer.created_at) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Last Activity</span>
                  <span class="info-value">{{ profileCustomer.last_activity ? timeAgo(profileCustomer.last_activity) : '—' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Status</span>
                  <span class="info-value">
                    <span class="status-badge" :class="customerStatus(profileCustomer)">
                      {{ customerStatusLabel(profileCustomer) }}
                    </span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Recent Inquiries -->
            <div class="profile-section">
              <h5 class="profile-section-title">Recent Inquiries</h5>

              <div v-if="profileLoading" class="profile-loading">
                <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                </svg>
              </div>

              <div v-else-if="profileInquiries.length > 0" class="inquiry-list">
                <div v-for="inq in profileInquiries" :key="inq.id" class="inquiry-item">
                  <div class="inquiry-item-left">
                    <span class="inquiry-item-id">INQ-{{ String(inq.id).padStart(5, '0') }}</span>
                    <span class="inquiry-item-cat">{{ inq.category }}</span>
                  </div>
                  <div class="inquiry-item-right">
                    <span class="status-badge" :class="`status-badge--${slugify(inq.status)}`">
                      {{ inq.status }}
                    </span>
                    <span class="inquiry-item-date">{{ timeAgo(inq.created_at) }}</span>
                  </div>
                </div>
              </div>

              <p v-else class="profile-empty">No inquiries submitted yet.</p>
            </div>

          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── State ─────────────────────────────────────────────────────────────────────
const customers = ref([]);
const loading   = ref(true);
const flash     = reactive({ message: '', type: 'success' });

const stats = ref({
  total:                0,
  newThisMonth:         0,
  withActiveInquiries:  0,
  completedJobs:        0,
});

const pagination = ref({
  current_page: 1,
  last_page:    1,
  total:        0,
  per_page:     15,
});

const filters = reactive({ search: '', sort: 'newest' });

// ── Profile Drawer ────────────────────────────────────────────────────────────
const showProfile      = ref(false);
const profileCustomer  = ref(null);
const profileInquiries = ref([]);
const profileLoading   = ref(false);

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchCustomers = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page,
      ...(filters.search && { search: filters.search }),
      ...(filters.sort   && { sort:   filters.sort }),
    });

    const res = await fetch(`${API_URL}/admin/customers?${params}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();

    customers.value  = data.data          ?? data.customers?.data ?? [];
    pagination.value = {
      current_page: data.current_page      ?? 1,
      last_page:    data.last_page         ?? 1,
      total:        data.total             ?? 0,
      per_page:     data.per_page          ?? 15,
    };

    if (data.stats) stats.value = {
      total:               data.stats.total                ?? 0,
      newThisMonth:        data.stats.new_this_month       ?? 0,
      withActiveInquiries: data.stats.with_active_inquiries ?? 0,
      completedJobs:       data.stats.completed_jobs       ?? 0,
    };

  } catch (err) {
    console.error('Failed to fetch customers:', err);
    showFlash('Failed to load customers.', 'error');
  } finally {
    loading.value = false;
  }
};

const fetchProfile = async (customerId) => {
  profileLoading.value = true;
  try {
    const res = await fetch(`${API_URL}/admin/customers/${customerId}/inquiries`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    profileInquiries.value = data.data ?? data ?? [];
  } catch (err) {
    console.error('Failed to fetch profile:', err);
    profileInquiries.value = [];
  } finally {
    profileLoading.value = false;
  }
};

// ── Profile Drawer ────────────────────────────────────────────────────────────
const openProfile = (customer) => {
  profileCustomer.value  = customer;
  profileInquiries.value = [];
  showProfile.value      = true;
  fetchProfile(customer.id);
  document.body.style.overflow = 'hidden';
};

const closeProfile = () => {
  showProfile.value            = false;
  profileCustomer.value        = null;
  document.body.style.overflow = '';
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const AVATAR_COLORS = [
  '#dbeafe', '#d1fae5', '#fef3c7', '#ede9fe',
  '#fce7f3', '#e0f2fe', '#fff7ed', '#f0fdf4',
];

const AVATAR_TEXT_COLORS = [
  '#1d4ed8', '#065f46', '#92400e', '#5b21b6',
  '#be185d', '#0369a1', '#c2410c', '#15803d',
];

const avatarColor = (customer) => {
  const idx = customer.id % AVATAR_COLORS.length;
  return AVATAR_COLORS[idx];
};

const avatarTextColor = (customer) => {
  const idx = customer.id % AVATAR_TEXT_COLORS.length;
  return AVATAR_TEXT_COLORS[idx];
};

const initials = (customer) => {
  const f = (customer.firstname ?? 'C')[0];
  const l = (customer.lastname  ?? '')[0] ?? '';
  return (f + l).toUpperCase();
};

const customerStatus = (customer) => {
  if ((customer.inquiries_count ?? 0) === 0) return 'status-badge--new';
  if ((customer.job_orders_count ?? 0) > 0)  return 'status-badge--active';
  return 'status-badge--returning';
};

const customerStatusLabel = (customer) => {
  if ((customer.inquiries_count ?? 0) === 0) return 'New';
  if ((customer.job_orders_count ?? 0) > 0)  return 'Active';
  return 'Returning';
};

const formatDate = (iso) => {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' });
};

const timeAgo = (iso) => {
  if (!iso) return '—';
  const diff  = Date.now() - new Date(iso).getTime();
  const mins  = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days  = Math.floor(diff / 86400000);
  if (mins  < 1)  return 'just now';
  if (mins  < 60) return `${mins}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days  < 7)  return `${days}d ago`;
  return formatDate(iso);
};

const slugify = (str) => (str ?? '').toLowerCase().replace(/\s+/g, '-');

const showFlash = (message, type = 'success') => {
  flash.message = message;
  flash.type    = type;
  setTimeout(() => { flash.message = ''; }, 3500);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchCustomers(page);
};

const visiblePages = computed(() => {
  const total   = pagination.value.last_page;
  const current = pagination.value.current_page;
  const pages   = [];
  for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) pages.push(i);
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
  searchTimer = setTimeout(() => fetchCustomers(1), 400);
};

onMounted(() => fetchCustomers());
</script>

<style scoped>
/* ── Page ── */
.customers-page {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
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

.page-subtitle { font-size: 0.8rem; color: #64748b; margin: 0; }

.header-actions { display: flex; align-items: center; gap: 0.6rem; }

.pill {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border-radius: 999px;
  padding: 0.3rem 0.85rem;
  font-size: 0.72rem;
  font-weight: 600;
}

.pill--blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

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
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 14px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon--blue   { background: #eff6ff; color: #2563eb; }
.stat-icon--green  { background: #f0fdf4; color: #16a34a; }
.stat-icon--amber  { background: #fffbeb; color: #d97706; }
.stat-icon--violet { background: #f5f3ff; color: #7c3aed; }

.stat-label { font-size: 0.72rem; color: #64748b; font-weight: 500; margin: 0 0 0.25rem; }
.stat-count { font-size: 1.6rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; margin: 0; }

/* ── Filters ── */
.filters-row {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.search-wrapper { position: relative; flex: 1; min-width: 200px; max-width: 360px; }

.search-icon {
  position: absolute;
  left: 0.6rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.5rem 0.75rem 0.5rem 2rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.75rem;
  color: #0f172a;
  background: #fff;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}

.search-input:focus {
  outline: none;
  border-color: #94a3b8;
  box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
}

.select-wrapper { position: relative; display: inline-flex; align-items: center; }

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
}

.filter-select:focus { outline: none; border-color: #94a3b8; }

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

.c-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8rem;
  text-align: left;
}

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

.table-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
  cursor: default;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f8fafc; }

.td { padding: 0.875rem 1rem; vertical-align: middle; color: #0f172a; }
.td--center { text-align: center; }

/* ── Customer Profile ── */
.customer-profile {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.customer-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #0f172a;
}

.customer-name  { font-weight: 600; font-size: 0.82rem; color: #0f172a; }
.customer-since { font-size: 0.68rem; color: #94a3b8; margin-top: 1px; }

.contact-email { font-size: 0.78rem; color: #334155; }
.contact-phone { font-size: 0.7rem; color: #64748b; margin-top: 1px; }

/* ── Count Badges ── */
.count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 28px;
  height: 24px;
  padding: 0 0.5rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
}

.count-badge--blue  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.count-badge--green { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* ── Dates ── */
.date-main { font-size: 0.75rem; color: #0f172a; font-weight: 500; }
.date-sub  { font-size: 0.68rem; color: #64748b; margin-top: 1px; }
.no-activity { font-size: 0.72rem; color: #cbd5e1; }

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

.status-badge--new       { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.status-badge--active    { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--returning { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

/* inquiry statuses reused from other views */
.status-badge--pending      { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-badge--acknowledged { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.status-badge--in-progress  { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
.status-badge--scheduled    { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.status-badge--completed    { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--cancelled    { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
.status-badge--converted    { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

/* ── Actions ── */
.btn-view {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.32rem 0.7rem;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #2563eb;
  border-radius: 7px;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-view:hover { background: #dbeafe; color: #1d4ed8; }

/* ── Empty ── */
.td--empty { padding: 4rem 1rem; text-align: center; }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  color: #94a3b8;
}

.empty-title { font-size: 0.9rem; font-weight: 600; color: #475569; }
.empty-sub   { font-size: 0.78rem; color: #94a3b8; max-width: 300px; line-height: 1.5; }

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
.page-info    { font-size: 0.72rem; color: #64748b; margin-right: 0.5rem; }

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

.page-btn:hover:not(:disabled) { background: #f1f5f9; border-color: #cbd5e1; }
.page-btn:disabled              { opacity: 0.4; cursor: not-allowed; }
.page-btn--active               { background: #0f172a; color: #fff; border-color: #0f172a; }

/* ── Drawer ── */
.drawer-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  z-index: 200;
  display: flex;
  justify-content: flex-end;
}

.drawer {
  width: 100%;
  max-width: 420px;
  height: 100%;
  background: #fff;
  box-shadow: -8px 0 40px rgba(0,0,0,0.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  flex-shrink: 0;
}

.drawer-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 0.25rem;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}

.modal-close:hover { background: #f1f5f9; color: #475569; }

.drawer-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* ── Profile Hero ── */
.profile-hero {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.profile-avatar-lg {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  font-size: 1.1rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #0f172a;
}

.profile-fullname { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.2rem; }
.profile-email    { font-size: 0.78rem; color: #64748b; margin: 0 0 0.1rem; }
.profile-phone    { font-size: 0.75rem; color: #94a3b8; margin: 0; }

/* ── Profile Stats ── */
.profile-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.profile-stat {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0.875rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.profile-stat-count {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
}

.profile-stat-label {
  font-size: 0.68rem;
  color: #64748b;
  font-weight: 500;
}

/* ── Profile Sections ── */
.profile-section { display: flex; flex-direction: column; gap: 0.75rem; }

.profile-section-title {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #64748b;
  margin: 0;
}

.info-grid { display: flex; flex-direction: column; gap: 0.5rem; }

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.78rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f8fafc;
}

.info-label { color: #64748b; }
.info-value { color: #0f172a; font-weight: 500; }

/* ── Inquiry List ── */
.profile-loading {
  display: flex;
  justify-content: center;
  padding: 1rem;
  color: #94a3b8;
}

.inquiry-list { display: flex; flex-direction: column; gap: 0.5rem; }

.inquiry-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.65rem 0.875rem;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 9px;
  gap: 0.5rem;
}

.inquiry-item-left  { display: flex; flex-direction: column; gap: 0.15rem; }
.inquiry-item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.2rem; }

.inquiry-item-id   { font-size: 0.75rem; font-weight: 700; color: #0f172a; }
.inquiry-item-cat  { font-size: 0.68rem; color: #64748b; }
.inquiry-item-date { font-size: 0.65rem; color: #94a3b8; }

.profile-empty { font-size: 0.78rem; color: #94a3b8; text-align: center; padding: 1rem 0; margin: 0; }

/* ── Spinner ── */
.spinner { animation: spin 0.8s linear infinite; color: #64748b; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-leave-to     { opacity: 0; }

.drawer-slide-enter-active { transition: all 0.3s ease; }
.drawer-slide-enter-from   { opacity: 0; }
.drawer-slide-leave-active { transition: all 0.25s ease; }
.drawer-slide-leave-to     { opacity: 0; }

.drawer-slide-enter-active .drawer {
  animation: slideInRight 0.3s ease forwards;
}

.drawer-slide-leave-active .drawer {
  animation: slideOutRight 0.25s ease forwards;
}

@keyframes slideInRight {
  from { transform: translateX(100%); }
  to   { transform: translateX(0); }
}

@keyframes slideOutRight {
  from { transform: translateX(0); }
  to   { transform: translateX(100%); }
}

/* ── Responsive ── */
@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 700px)  {
  .stats-grid     { grid-template-columns: 1fr 1fr; }
  .customers-page { padding: 1rem; }
  .drawer         { max-width: 100%; }
}
@media (max-width: 480px)  { .stats-grid { grid-template-columns: 1fr; } }
</style>