<!-- src/views/admin/TechniciansView.vue -->
<template>
  <div class="tech-page">

    <!-- ── Page Header ── -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Technicians</h1>
        <p class="page-subtitle">Assign jobs and keep track of each technician's workload.</p>
      </div>

      <div class="header-actions">
        <!-- Summary Pills -->
        <span class="pill pill--green">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          {{ technicians.length }} technicians
        </span>

        <span class="pill pill--blue">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 7V5a2 2 0 00-4 0v2M8 7V5a2 2 0 00-4 0v2" stroke="currentColor" stroke-width="2"/>
          </svg>
          {{ totalActiveJobs }} active job orders
        </span>

        <!-- Add Technician Button -->
        <button class="btn-add" @click="openAddModal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          Add Technician
        </button>
      </div>
    </div>

    <!-- ── Flash ── -->
    <transition name="slide-down">
      <div v-if="flash.message" class="flash" :class="`flash--${flash.type}`">
        {{ flash.message }}
      </div>
    </transition>

    <!-- ── Legend ── -->
    <div class="legend-bar">
      <div class="legend-items">
        <span class="legend-label">Status:</span>
        <span class="badge-legend badge-legend--available">Available</span>
        <span class="badge-legend badge-legend--in_progress">In Progress</span>
        <span class="badge-legend badge-legend--completed">Completed</span>
        <span class="badge-legend badge-legend--scheduled">Scheduled</span>
      </div>
      <p class="legend-hint">Use "Add Technician" to create profiles, then assign job orders from the table.</p>
    </div>

    <!-- ── Table ── -->
    <div class="table-card">

      <div v-if="loading" class="table-loading">
        <svg class="spinner" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
        </svg>
        <span>Loading technicians…</span>
      </div>

      <div class="table-scroll">
        <table class="tech-table">
          <thead>
            <tr>
              <th class="th">Technician</th>
              <th class="th">Contact</th>
              <th class="th">Current Job</th>
              <th class="th th--center">Status</th>
              <th class="th th--center" style="width:180px">Actions</th>
            </tr>
          </thead>

          <tbody>
            <template v-if="technicians.length > 0">
              <tr v-for="tech in technicians" :key="tech.id" class="table-row">

                <!-- Technician -->
                <td class="td">
                  <div class="tech-profile">
                    <div class="tech-avatar">{{ initials(tech) }}</div>
                    <div>
                      <div class="tech-name">{{ tech.name ?? tech.user?.firstname ?? 'Technician' }}</div>
                      <div class="tech-cert">{{ tech.certifications ?? 'General technician' }}</div>
                    </div>
                  </div>
                </td>

                <!-- Contact -->
                <td class="td">
                  <div class="contact-phone">{{ tech.user?.phone ?? '—' }}</div>
                  <div class="contact-email">{{ tech.user?.email ?? '—' }}</div>
                </td>

                <!-- Current Job -->
                <td class="td">
                  <template v-if="latestJob(tech)">
                    <div class="job-id">JO-{{ String(latestJob(tech).id).padStart(5, '0') }}</div>
                    <div class="job-desc">{{ latestJob(tech).issue_description ?? 'No description' }}</div>
                  </template>
                  <span v-else class="no-assignment">No current assignment</span>
                </td>

                <!-- Status -->
                <td class="td td--center">
                  <span class="status-badge" :class="`status-badge--${techStatus(tech)}`">
                    {{ formatStatus(techStatus(tech)) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="td td--center">
                  <div class="action-row">

                    <!-- Assign Job Order -->
                    <button class="btn-assign-job" @click="openAssignModal(tech)">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                      </svg>
                      Assign JO
                    </button>

                    <!-- Workload count -->
                    <span class="workload-count">
                      {{ tech.job_orders_count ?? 0 }} {{ tech.job_orders_count === 1 ? 'job' : 'jobs' }}
                    </span>

                    <!-- More actions dropdown -->
                    <div class="dropdown" :class="{ 'dropdown--open': openDropdown === tech.id }">
                      <button
                        class="btn-dots"
                        @click.stop="toggleDropdown(tech.id)"
                        title="More actions"
                      >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                          <circle cx="12" cy="5"  r="1.5" fill="currentColor"/>
                          <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                          <circle cx="12" cy="19" r="1.5" fill="currentColor"/>
                        </svg>
                      </button>

                      <transition name="dropdown-fade">
                        <div v-if="openDropdown === tech.id" class="dropdown-menu">
                          <router-link
                            :to="{ name: 'admin.technicians.edit', params: { id: tech.id } }"
                            class="dropdown-item"
                            @click="closeDropdown"
                          >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Edit technician
                          </router-link>

                          <button
                            class="dropdown-item dropdown-item--danger"
                            @click="confirmDelete(tech)"
                          >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                              <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Delete technician
                          </button>
                        </div>
                      </transition>
                    </div>

                  </div>
                </td>
              </tr>
            </template>

            <tr v-else-if="!loading">
              <td colspan="5" class="td--empty">
                <div class="empty-state">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <div class="empty-title">No technicians recorded yet.</div>
                  <div class="empty-sub">Use "Add Technician" to create one.</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Add Technician Modal ── -->
    <transition name="modal-fade">
      <div v-if="showAddModal" class="modal-backdrop" @click.self="closeAddModal">
        <div class="modal">
          <div class="modal-header">
            <h3 class="modal-title">Add Technician</h3>
            <button class="modal-close" @click="closeAddModal">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAddTechnician" class="modal-body">
            <div class="field-group">
              <label class="field-label">First Name <span class="req">*</span></label>
              <input v-model="addForm.firstname" type="text" class="field-input" :class="{ 'field-error': addErrors.firstname }" placeholder="Juan" />
              <span v-if="addErrors.firstname" class="error-msg">{{ addErrors.firstname }}</span>
            </div>

            <div class="field-group">
              <label class="field-label">Last Name <span class="req">*</span></label>
              <input v-model="addForm.lastname" type="text" class="field-input" :class="{ 'field-error': addErrors.lastname }" placeholder="Dela Cruz" />
              <span v-if="addErrors.lastname" class="error-msg">{{ addErrors.lastname }}</span>
            </div>

            <div class="field-group">
              <label class="field-label">Email <span class="req">*</span></label>
              <input v-model="addForm.email" type="email" class="field-input" :class="{ 'field-error': addErrors.email }" placeholder="juan@example.com" />
              <span v-if="addErrors.email" class="error-msg">{{ addErrors.email }}</span>
            </div>

            <div class="field-group">
              <label class="field-label">Phone</label>
              <input v-model="addForm.phone" type="tel" class="field-input" placeholder="09XX XXX XXXX" />
            </div>

            <div class="field-group field-group--full">
              <label class="field-label">Certifications / Specialization</label>
              <input v-model="addForm.certifications" type="text" class="field-input" placeholder="e.g. Electronics, HVAC, Networking" />
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeAddModal">Cancel</button>
              <button type="submit" class="btn-submit" :disabled="addSubmitting">
                <svg v-if="addSubmitting" class="spinner spinner--sm" width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                </svg>
                <span v-else>Add Technician</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- ── Assign Job Order Modal ── -->
    <transition name="modal-fade">
      <div v-if="showAssignModal" class="modal-backdrop" @click.self="closeAssignModal">
        <div class="modal">
          <div class="modal-header">
            <h3 class="modal-title">Assign Job Order</h3>
            <button class="modal-close" @click="closeAssignModal">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <p class="assign-target">
              Assigning to: <strong>{{ assignTarget?.name ?? assignTarget?.user?.firstname }}</strong>
            </p>

            <div class="field-group field-group--full">
              <label class="field-label">Select Approved Quotation <span class="req">*</span></label>
              <div class="select-wrapper">
                <select v-model="assignForm.quotation_id" class="field-input field-select" :class="{ 'field-error': assignErrors.quotation_id }">
                  <option value="">Choose a quotation…</option>
                  <option v-for="q in approvedQuotations" :key="q.id" :value="q.id">
                    Q-{{ String(q.id).padStart(5, '0') }} — {{ q.client_name }} ({{ q.project_title }})
                  </option>
                </select>
                <span class="select-chevron">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </span>
              </div>
              <span v-if="assignErrors.quotation_id" class="error-msg">{{ assignErrors.quotation_id }}</span>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeAssignModal">Cancel</button>
              <button class="btn-submit" :disabled="assignSubmitting || !assignForm.quotation_id" @click="submitAssign">
                <svg v-if="assignSubmitting" class="spinner spinner--sm" width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                </svg>
                <span v-else>Assign</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- ── Delete Confirm Modal ── -->
    <transition name="modal-fade">
      <div v-if="showDeleteModal" class="modal-backdrop" @click.self="showDeleteModal = false">
        <div class="modal modal--sm">
          <div class="modal-header">
            <h3 class="modal-title modal-title--danger">Delete Technician</h3>
            <button class="modal-close" @click="showDeleteModal = false">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <p class="delete-warning">
              Are you sure you want to delete <strong>{{ deleteTarget?.name ?? deleteTarget?.user?.firstname }}</strong>?
              This cannot be undone.
            </p>
            <div class="modal-footer">
              <button class="btn-cancel" @click="showDeleteModal = false">Cancel</button>
              <button class="btn-danger" :disabled="deleteSubmitting" @click="submitDelete">
                <svg v-if="deleteSubmitting" class="spinner spinner--sm" width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                </svg>
                <span v-else>Delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── State ─────────────────────────────────────────────────────────────────────
const technicians        = ref([]);
const approvedQuotations = ref([]);
const loading            = ref(true);
const flash              = reactive({ message: '', type: 'success' });
const openDropdown       = ref(null);

// ── Add Modal ─────────────────────────────────────────────────────────────────
const showAddModal   = ref(false);
const addSubmitting  = ref(false);
const addErrors      = reactive({});
const addForm        = reactive({ firstname: '', lastname: '', email: '', phone: '', certifications: '' });

// ── Assign Modal ──────────────────────────────────────────────────────────────
const showAssignModal  = ref(false);
const assignTarget     = ref(null);
const assignSubmitting = ref(false);
const assignErrors     = reactive({});
const assignForm       = reactive({ quotation_id: '' });

// ── Delete Modal ──────────────────────────────────────────────────────────────
const showDeleteModal  = ref(false);
const deleteTarget     = ref(null);
const deleteSubmitting = ref(false);

// ── Computed ──────────────────────────────────────────────────────────────────
const totalActiveJobs = computed(() =>
  technicians.value.reduce((sum, t) => sum + (t.job_orders_count ?? 0), 0)
);

// ── Fetch ─────────────────────────────────────────────────────────────────────
const fetchData = async () => {
  loading.value = true;
  try {
    const [techRes, quotRes] = await Promise.all([
      fetch(`${API_URL}/admin/technicians`),
      fetch(`${API_URL}/admin/quotations?status=approved&per_page=999`),
    ]);

    if (!techRes.ok) throw new Error(`HTTP ${techRes.status}`);
    const techData = await techRes.json();
    technicians.value = techData.data ?? techData;

    if (quotRes.ok) {
      const quotData = await quotRes.json();
      approvedQuotations.value = quotData.data ?? quotData;
    }

  } catch (err) {
    console.error('Failed to fetch technicians:', err);
    showFlash('Failed to load technicians.', 'error');
  } finally {
    loading.value = false;
  }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const initials = (tech) => {
  const f = (tech.user?.firstname ?? 'T')[0];
  const l = (tech.user?.lastname  ?? '')[0] ?? '';
  return (f + l).toUpperCase();
};

const latestJob = (tech) => tech.job_orders?.[0] ?? null;

const techStatus = (tech) => latestJob(tech)?.status ?? 'available';

const formatStatus = (status) =>
  status === 'available'
    ? 'Available'
    : status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

const showFlash = (message, type = 'success') => {
  flash.message = message;
  flash.type    = type;
  setTimeout(() => { flash.message = ''; }, 3500);
};

// ── Dropdown ──────────────────────────────────────────────────────────────────
const toggleDropdown = (id) => {
  openDropdown.value = openDropdown.value === id ? null : id;
};

const closeDropdown = () => { openDropdown.value = null; };

const handleOutsideClick = () => { openDropdown.value = null; };

// ── Add Technician ────────────────────────────────────────────────────────────
const openAddModal = () => {
  Object.assign(addForm, { firstname: '', lastname: '', email: '', phone: '', certifications: '' });
  Object.keys(addErrors).forEach(k => delete addErrors[k]);
  showAddModal.value = true;
};

const closeAddModal = () => { showAddModal.value = false; };

const submitAddTechnician = async () => {
  Object.keys(addErrors).forEach(k => delete addErrors[k]);

  if (!addForm.firstname.trim()) { addErrors.firstname = 'First name is required.'; }
  if (!addForm.lastname.trim())  { addErrors.lastname  = 'Last name is required.'; }
  if (!addForm.email.trim())     { addErrors.email     = 'Email is required.'; }
  if (Object.keys(addErrors).length) return;

  addSubmitting.value = true;
  try {
    const res = await fetch(`${API_URL}/admin/technicians`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify(addForm),
    });

    if (!res.ok) {
      const data = await res.json();
      if (res.status === 422 && data?.errors) {
        Object.entries(data.errors).forEach(([k, v]) => {
          addErrors[k] = Array.isArray(v) ? v[0] : v;
        });
        return;
      }
      throw new Error(`HTTP ${res.status}`);
    }

    showFlash('Technician added successfully.', 'success');
    closeAddModal();
    await fetchData();

  } catch (err) {
    console.error('Add failed:', err);
    showFlash('Failed to add technician.', 'error');
  } finally {
    addSubmitting.value = false;
  }
};

// ── Assign Job Order ──────────────────────────────────────────────────────────
const openAssignModal = (tech) => {
  assignTarget.value   = tech;
  assignForm.quotation_id = '';
  Object.keys(assignErrors).forEach(k => delete assignErrors[k]);
  showAssignModal.value = true;
};

const closeAssignModal = () => { showAssignModal.value = false; assignTarget.value = null; };

const submitAssign = async () => {
  if (!assignForm.quotation_id) {
    assignErrors.quotation_id = 'Please select a quotation.';
    return;
  }

  assignSubmitting.value = true;
  try {
    const res = await fetch(`${API_URL}/admin/technicians/${assignTarget.value.id}/assign-job`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ quotation_id: assignForm.quotation_id }),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    showFlash('Job order assigned successfully.', 'success');
    closeAssignModal();
    await fetchData();

  } catch (err) {
    console.error('Assign failed:', err);
    showFlash('Failed to assign job order.', 'error');
  } finally {
    assignSubmitting.value = false;
  }
};

// ── Delete ────────────────────────────────────────────────────────────────────
const confirmDelete = (tech) => {
  deleteTarget.value   = tech;
  showDeleteModal.value = true;
  closeDropdown();
};

const submitDelete = async () => {
  deleteSubmitting.value = true;
  try {
    const res = await fetch(`${API_URL}/admin/technicians/${deleteTarget.value.id}`, {
      method:  'DELETE',
      headers: { 'Accept': 'application/json' },
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    showFlash('Technician deleted.', 'success');
    showDeleteModal.value  = false;
    deleteTarget.value     = null;
    await fetchData();

  } catch (err) {
    console.error('Delete failed:', err);
    showFlash('Failed to delete technician.', 'error');
  } finally {
    deleteSubmitting.value = false;
  }
};

onMounted(() => {
  fetchData();
  document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});
</script>

<style scoped>
/* ── Page ── */
.tech-page {
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

.page-subtitle {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

/* ── Pills ── */
.pill {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border-radius: 999px;
  padding: 0.3rem 0.85rem;
  font-size: 0.72rem;
  font-weight: 600;
}

.pill--green { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.pill--blue  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

/* ── Add Button ── */
.btn-add {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 1rem;
  background: #0f172a;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
}

.btn-add:hover { background: #1e293b; }

/* ── Flash ── */
.flash {
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-size: 0.85rem;
}

.flash--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.flash--error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

/* ── Legend ── */
.legend-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 12px;
  padding: 0.875rem 1.25rem;
}

.legend-items {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.legend-label {
  font-size: 0.72rem;
  color: #94a3b8;
}

.badge-legend {
  border-radius: 999px;
  padding: 0.25rem 0.65rem;
  font-size: 0.68rem;
  font-weight: 600;
}

.badge-legend--available   { background: #0f172a; color: #fff; }
.badge-legend--in_progress { background: #fef9c3; color: #854d0e; }
.badge-legend--completed   { background: #d1fae5; color: #065f46; }
.badge-legend--scheduled   { background: #dbeafe; color: #1d4ed8; }

.legend-hint {
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

.tech-table {
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
  vertical-align: top;
  color: #0f172a;
}

.td--center { text-align: center; vertical-align: middle; }

/* ── Tech Profile ── */
.tech-profile {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.tech-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #d1fae5;
  color: #065f46;
  font-size: 0.7rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.tech-name { font-weight: 600; font-size: 0.8rem; color: #0f172a; }
.tech-cert { font-size: 0.7rem; color: #64748b; margin-top: 1px; }

/* ── Contact ── */
.contact-phone { font-size: 0.78rem; color: #0f172a; }
.contact-email { font-size: 0.7rem; color: #64748b; margin-top: 1px; }

/* ── Job ── */
.job-id   { font-size: 0.78rem; font-weight: 700; color: #0f172a; }
.job-desc {
  font-size: 0.7rem;
  color: #64748b;
  margin-top: 2px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.no-assignment { font-size: 0.72rem; color: #94a3b8; }

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

.status-badge--available   { background: #0f172a; color: #fff; }
.status-badge--in_progress { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
.status-badge--completed   { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.status-badge--scheduled   { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.status-badge--on-site     { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }

/* ── Actions ── */
.action-row {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  justify-content: center;
}

.btn-assign-job {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #15803d;
  border-radius: 7px;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-assign-job:hover { background: #dcfce7; border-color: #86efac; }

.workload-count {
  font-size: 0.68rem;
  color: #94a3b8;
  white-space: nowrap;
}

/* ── Dropdown ── */
.dropdown { position: relative; }

.btn-dots {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 7px;
  border: none;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-dots:hover { background: #f1f5f9; }

.dropdown-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 4px);
  width: 168px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  z-index: 50;
  overflow: hidden;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 0.875rem;
  font-size: 0.75rem;
  color: #374151;
  text-decoration: none;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}

.dropdown-item:hover { background: #f8fafc; }
.dropdown-item--danger { color: #dc2626; }
.dropdown-item--danger:hover { background: #fff1f2; }

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

/* ── Modal ── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 1rem;
}

.modal {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 24px 60px rgba(0,0,0,0.15);
  overflow: hidden;
}

.modal--sm { max-width: 360px; }

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.modal-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.modal-title--danger { color: #dc2626; }

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

.modal-body {
  padding: 1.25rem 1.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.assign-target {
  grid-column: 1 / -1;
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.delete-warning {
  grid-column: 1 / -1;
  font-size: 0.875rem;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* ── Modal Fields ── */
.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.field-group--full { grid-column: 1 / -1; }

.field-label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #334155;
}

.req { color: #ef4444; }

.field-input {
  padding: 0.65rem 0.875rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.82rem;
  color: #0f172a;
  background: #fff;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
  width: 100%;
}

.field-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.field-input.field-error {
  border-color: #ef4444;
}

.field-select { appearance: none; padding-right: 2rem; cursor: pointer; }

.select-wrapper { position: relative; }

.select-chevron {
  pointer-events: none;
  position: absolute;
  right: 0.6rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  display: flex;
  align-items: center;
}

.error-msg { font-size: 0.72rem; color: #ef4444; }

/* ── Modal Footer ── */
.modal-footer {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid #f1f5f9;
  margin-top: 0.25rem;
}

.btn-cancel {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #fff;
  font-size: 0.82rem;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
}

.btn-cancel:hover { background: #f8fafc; }

.btn-submit {
  padding: 0.5rem 1.25rem;
  background: #0f172a;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.btn-submit:hover:not(:disabled) { background: #1e293b; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-danger {
  padding: 0.5rem 1.25rem;
  background: #dc2626;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.btn-danger:hover:not(:disabled) { background: #b91c1c; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Spinner ── */
.spinner     { animation: spin 0.8s linear infinite; color: #64748b; }
.spinner--sm { color: #fff; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-leave-to     { opacity: 0; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.25s ease; }
.modal-fade-enter-from,   .modal-fade-leave-to     { opacity: 0; }

.dropdown-fade-enter-active { transition: all 0.15s ease; }
.dropdown-fade-enter-from   { opacity: 0; transform: translateY(-6px); }
.dropdown-fade-leave-active { transition: all 0.1s ease; }
.dropdown-fade-leave-to     { opacity: 0; }

/* ── Responsive ── */
.modal-body { grid-template-columns: 1fr 1fr; }

@media (max-width: 600px) {
  .tech-page   { padding: 1rem; }
  .modal-body  { grid-template-columns: 1fr; }
  .page-header { flex-direction: column; align-items: flex-start; }
}
</style>