<template>
  <div class="services-page">

    <div class="page-header">
      <div class="header-content">
        <h1>Manage Services</h1>
        <p class="subtitle">Create and manage your service offerings</p>
      </div>
      <router-link to="/admin/services/create" class="btn-primary">+ Add Service</router-link>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Total" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ services.length }}</div>
          <div class="stat-label">Total Services</div>
        </div>
      </div>
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Active" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ activeCount }}</div>
          <div class="stat-label">Active Services</div>
        </div>
      </div>
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Featured" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ featuredCount }}</div>
          <div class="stat-label">Featured Services</div>
        </div>
      </div>
    </div>

    <div class="services-table-container">
      <div class="table-header">
        <input type="text" v-model="searchQuery" placeholder="Search services..." class="search-input" />
        <select v-model="filterStatus" class="filter-select">
          <option value="all">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="draft">Draft</option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="feedback-state">
        <div class="spinner-lg"></div>
        <p>Loading services...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="feedback-state error">
        <p>{{ error }}</p>
        <button class="btn-retry" @click="fetchServices">Retry</button>
      </div>

      <div v-else class="table-wrapper">
        <table class="services-table">
          <thead>
            <tr>
              <th>Service</th>
              <th>Category</th>
              <th>Status</th>
              <th>Featured</th>
              <th>Order</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in filteredServices" :key="service.id">
              <td>
                <div class="service-info">
                  <img
                    :src="service.image ? `${baseURL}/storage/${service.image}` : 'https://placehold.co/50x50'"
                    :alt="service.name"
                    class="service-img"
                  />
                  <div>
                    <div class="service-name">{{ service.name }}</div>
                    <div class="service-desc">{{ truncate(service.subtitle || service.description, 50) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="category-badge">{{ service.category?.name ?? '—' }}</span>
              </td>
              <td>
                <span class="status-badge" :class="service.status">{{ capitalize(service.status) }}</span>
              </td>
              <td>
                <button
                  class="toggle-btn"
                  :class="{ active: service.featured }"
                  :disabled="togglingId === service.id"
                  @click="toggleFeatured(service)"
                >
                  {{ service.featured ? 'Yes' : 'No' }}
                </button>
              </td>
              <td><span class="order-badge">{{ service.order ?? 0 }}</span></td>
              <td><span class="date">{{ formatDate(service.created_at) }}</span></td>
              <td>
                <div class="action-buttons">
                  <router-link :to="`/admin/services/${service.id}`" class="btn-icon btn-view">View</router-link>
                  <router-link :to="`/admin/services/${service.id}/edit`" class="btn-icon btn-edit">Edit</router-link>
                  <button class="btn-icon btn-delete" @click="confirmDelete(service)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredServices.length === 0" class="empty-state">
          <img src="https://placehold.co/120x120" alt="No services" class="empty-img" />
          <h3>No services found</h3>
          <p>{{ searchQuery ? 'Try adjusting your search' : 'Get started by adding your first service' }}</p>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
      <div class="modal-container">
        <div class="modal-header">
          <h2>Delete Service</h2>
          <button class="close-btn" @click="showDeleteModal = false">×</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong>{{ serviceToDelete?.name }}</strong>? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button class="btn-secondary" @click="showDeleteModal = false">Cancel</button>
          <button class="btn-danger" :disabled="isDeleting" @click="deleteService">
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios, { baseURL } from '@/api/axios';

const services     = ref([]);
const loading      = ref(false);
const error        = ref(null);
const togglingId   = ref(null);
const isDeleting   = ref(false);

const searchQuery     = ref('');
const filterStatus    = ref('all');
const showDeleteModal = ref(false);
const serviceToDelete = ref(null);

// ── Computed ──────────────────────────────────────────────
const filteredServices = computed(() => {
  return services.value.filter(s => {
    const matchesSearch = s.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
      || s.subtitle?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = filterStatus.value === 'all' || s.status === filterStatus.value;
    return matchesSearch && matchesStatus;
  });
});

const activeCount   = computed(() => services.value.filter(s => s.status === 'active').length);
const featuredCount = computed(() => services.value.filter(s => s.featured === true).length);

// ── Helpers ───────────────────────────────────────────────
const truncate   = (text, len) => text?.length > len ? text.substring(0, len) + '...' : (text ?? '');
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : '—';
const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

// ── Actions ───────────────────────────────────────────────
const toggleFeatured = async (service) => {
  togglingId.value = service.id;
  try {
    await axios.patch(`/admin/services/${service.id}`, { featured: !service.featured });
    service.featured = !service.featured;
  } catch (err) {
    console.error('Failed to toggle featured', err);
  } finally {
    togglingId.value = null;
  }
};

const confirmDelete = (service) => {
  serviceToDelete.value = service;
  showDeleteModal.value  = true;
};

const deleteService = async () => {
  isDeleting.value = true;
  try {
    await axios.delete(`/admin/services/${serviceToDelete.value.id}`);
    services.value    = services.value.filter(s => s.id !== serviceToDelete.value.id);
    showDeleteModal.value  = false;
    serviceToDelete.value  = null;
  } catch (err) {
    console.error('Failed to delete service', err);
  } finally {
    isDeleting.value = false;
  }
};

const fetchServices = async () => {
  loading.value = true;
  error.value   = null;
  try {
    const { data } = await axios.get('/admin/services');
    services.value = data.data;
  } catch (err) {
    error.value = 'Failed to fetch services. Please try again.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchServices);
</script>

<style scoped>
.services-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.header-content h1 { font-size: 2rem; color: #1a1a1a; margin: 0 0 0.25rem 0; }
.subtitle { color: #666; margin: 0; font-size: 0.95rem; }

.btn-primary { background: linear-gradient(135deg, #00ff88 0%, #00cc6f 100%); color: #0a1f1a; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,255,136,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,255,136,0.4); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
.stat-img { width: 60px; height: 60px; object-fit: contain; border-radius: 12px; }
.stat-value { font-size: 2rem; font-weight: 700; color: #1a1a1a; line-height: 1; margin-bottom: 0.25rem; }
.stat-label { color: #666; font-size: 0.9rem; }

.services-table-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.table-header { padding: 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; gap: 1rem; flex-wrap: wrap; }
.search-input { flex: 1; min-width: 200px; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.3s ease; }
.filter-select { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; background: white; cursor: pointer; }
.search-input:focus, .filter-select:focus { outline: none; border-color: #00ff88; }

.feedback-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; }
.feedback-state.error { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.btn-retry:hover { border-color: #00ff88; }

.spinner-lg { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.table-wrapper { overflow-x: auto; }
.services-table { width: 100%; border-collapse: collapse; }
.services-table thead { background: #f9fafb; }
.services-table th { padding: 1rem 1.5rem; text-align: left; font-weight: 600; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; }
.services-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.services-table tbody tr:hover { background: #f9fafb; }

.service-info { display: flex; align-items: center; gap: 1rem; }
.service-img { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
.service-name { font-weight: 600; color: #1a1a1a; margin-bottom: 0.25rem; font-size: 0.95rem; }
.service-desc { font-size: 0.85rem; color: #666; }

.category-badge { display: inline-block; padding: 0.375rem 0.75rem; background: #e6fcf3; color: #00805c; border-radius: 6px; font-size: 0.85rem; font-weight: 500; }
.order-badge { display: inline-block; padding: 0.25rem 0.6rem; background: #f3f4f6; color: #444; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
.status-badge { display: inline-block; padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
.status-badge.active { background: #d1fae5; color: #065f46; }
.status-badge.inactive { background: #fee2e2; color: #991b1b; }
.status-badge.draft { background: #f3f4f6; color: #555; }

.toggle-btn { background: transparent; border: 1px solid #e5e7eb; border-radius: 6px; padding: 0.375rem 0.75rem; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; }
.toggle-btn.active { background: #fef3c7; border-color: #fbbf24; color: #92400e; }
.toggle-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.date { color: #666; font-size: 0.9rem; }

.action-buttons { display: flex; gap: 0.5rem; }
.btn-icon { border: 1px solid #e5e7eb; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; cursor: pointer; text-decoration: none; transition: all 0.3s ease; background: transparent; display: inline-flex; align-items: center; color: #333; }
.btn-view:hover { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
.btn-edit:hover { background: #e6fcf3; border-color: #00ff88; color: #00805c; }
.btn-delete:hover { background: #fee2e2; border-color: #ef4444; color: #991b1b; }

.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-img { width: 120px; height: auto; margin-bottom: 1rem; opacity: 0.6; }
.empty-state h3 { color: #1a1a1a; margin: 0 0 0.5rem 0; }
.empty-state p { color: #666; margin: 0; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem; }
.modal-container { background: white; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h2 { margin: 0; font-size: 1.2rem; color: #1a1a1a; }
.close-btn { background: transparent; border: none; font-size: 1.75rem; color: #666; cursor: pointer; line-height: 1; }
.modal-body { padding: 1.5rem; }
.modal-body p { margin: 0; color: #444; line-height: 1.6; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1.5rem; border-top: 1px solid #e5e7eb; }
.btn-secondary { padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; background: #f3f4f6; color: #666; border: none; transition: all 0.2s ease; }
.btn-secondary:hover { background: #e5e7eb; color: #1a1a1a; }
.btn-danger { padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; background: #ef4444; color: white; transition: all 0.2s ease; }
.btn-danger:hover:not(:disabled) { background: #dc2626; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 768px) {
  .services-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: flex-start; }
  .stats-grid { grid-template-columns: 1fr; }
  .table-header { flex-direction: column; }
  .search-input { min-width: 100%; }
}
</style>