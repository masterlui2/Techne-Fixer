<template>
  <div class="service-show-page">

    <div class="page-header">
      <router-link to="/admin/services" class="back-link">← Back to Services</router-link>
      <div class="header-actions" v-if="!isLoading && service">
        <router-link :to="`/admin/services/${service.id}/edit`" class="btn-secondary">Edit</router-link>
        <button class="btn-danger" @click="showDeleteModal = true">Delete</button>
      </div>
    </div>

    <div v-if="isLoading" class="loading-state">
      <div class="spinner-lg"></div>
      <p>Loading service...</p>
    </div>

    <div v-else-if="loadError" class="error-state">
      <p>{{ loadError }}</p>
      <button class="btn-retry" @click="fetchService">Retry</button>
    </div>

    <div v-else class="show-container">

      <!-- Service Header -->
      <div class="service-header-card">
        <img
          :src="service.image ? `/storage/${service.image}` : 'https://placehold.co/300x300'"
          :alt="service.name"
          class="service-image"
        />
        <div class="service-header-info">
          <div class="service-meta">
            <span class="category-badge">{{ service.category?.name ?? '—' }}</span>
            <span class="status-badge" :class="service.status">{{ capitalize(service.status) }}</span>
            <span v-if="service.featured" class="featured-badge">⭐ Featured</span>
          </div>
          <h1>{{ service.name }}</h1>
          <p v-if="service.subtitle" class="service-subtitle">{{ service.subtitle }}</p>
          <p class="service-description">{{ service.description }}</p>
        </div>
      </div>

      <!-- Details -->
      <div class="details-grid">

        <div class="detail-card">
          <h2>Service Details</h2>
          <div class="detail-list">
            <div class="detail-item">
              <span class="detail-label">Category</span>
              <span class="detail-value">{{ service.category?.name ?? '—' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Status</span>
              <span class="status-badge" :class="service.status">{{ capitalize(service.status) }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Featured</span>
              <span class="detail-value">{{ service.featured ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Display Order</span>
              <span class="detail-value">{{ service.order ?? 0 }}</span>
            </div>
            <div class="detail-item" v-if="service.icon">
              <span class="detail-label">Icon</span>
              <span class="detail-value">{{ service.icon }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Slug</span>
              <span class="detail-value slug">{{ service.slug }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Created</span>
              <span class="detail-value">{{ formatDate(service.created_at) }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Last Updated</span>
              <span class="detail-value">{{ formatDate(service.updated_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Scope of Works grouped by Service Type -->
        <div class="detail-card" v-if="service.serviceTypes?.length">
          <h2>Scope of Works</h2>
          <div v-for="type in service.serviceTypes" :key="type.id" class="scope-group">
            <div class="scope-type-label">
              <span class="type-dot" :style="{ background: type.color || '#00ff88' }"></span>
              {{ type.name }}
            </div>
            <ul class="scope-list">
              <li
                v-for="(work, i) in service.scopeOfWorks?.filter(w => w.service_type_id === type.id)"
                :key="i"
              >{{ work.description }}</li>
              <li v-if="!service.scopeOfWorks?.filter(w => w.service_type_id === type.id).length" class="empty-scope">
                No scope items defined.
              </li>
            </ul>
          </div>
        </div>

        <!-- Long description -->
        <div class="detail-card full-width" v-if="service.long_description">
          <h2>Full Description</h2>
          <p class="long-desc">{{ service.long_description }}</p>
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
          <p>Are you sure you want to delete <strong>{{ service?.name }}</strong>? This action cannot be undone.</p>
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
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from '@/api/axios';

const router = useRouter();
const route  = useRoute();

const service         = ref(null);
const isLoading       = ref(true);
const loadError       = ref(null);
const isDeleting      = ref(false);
const showDeleteModal = ref(false);

// ── Fetch ─────────────────────────────────────────────────
const fetchService = async () => {
  isLoading.value = true;
  loadError.value = null;
  try {
    const { data } = await axios.get(`/admin/services/${route.params.id}`);
    service.value = data.data;
  } catch (err) {
    loadError.value = 'Failed to load service. Please try again.';
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

// ── Delete ────────────────────────────────────────────────
const deleteService = async () => {
  isDeleting.value = true;
  try {
    await axios.delete(`/admin/services/${route.params.id}`);
    router.push('/admin/services');
  } catch (err) {
    console.error('Failed to delete service:', err);
    showDeleteModal.value = false;
  } finally {
    isDeleting.value = false;
  }
};

// ── Helpers ───────────────────────────────────────────────
const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
const formatDate = (date) => date
  ? new Date(date).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
  : '—';

onMounted(fetchService);
</script>

<style scoped>
.service-show-page { padding: 2rem; max-width: 1100px; margin: 0 auto; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.back-link { color: #666; text-decoration: none; font-size: 0.9rem; transition: color 0.2s ease; }
.back-link:hover { color: #00805c; }
.header-actions { display: flex; gap: 0.75rem; }

.loading-state, .error-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; }
.error-state { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.spinner-lg { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.show-container { display: flex; flex-direction: column; gap: 1.5rem; }

.service-header-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 2rem; display: flex; gap: 2rem; align-items: flex-start; }
.service-image { width: 200px; height: 200px; object-fit: cover; border-radius: 12px; flex-shrink: 0; }
.service-header-info { flex: 1; }
.service-meta { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem; }
.service-header-info h1 { font-size: 1.75rem; color: #1a1a1a; margin: 0 0 0.25rem 0; }
.service-subtitle { color: #555; font-size: 1rem; margin: 0 0 0.75rem 0; }
.service-description { color: #666; line-height: 1.6; margin: 0; }

.details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
.detail-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem; }
.detail-card.full-width { grid-column: 1 / -1; }
.detail-card h2 { font-size: 1rem; font-weight: 600; color: #1a1a1a; margin: 0 0 1.25rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6; }
.detail-list { display: flex; flex-direction: column; gap: 1rem; }
.detail-item { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.detail-label { font-size: 0.9rem; color: #666; flex-shrink: 0; }
.detail-value { font-weight: 500; color: #1a1a1a; font-size: 0.95rem; text-align: right; }
.detail-value.slug { font-family: monospace; font-size: 0.85rem; background: #f3f4f6; padding: 0.2rem 0.5rem; border-radius: 4px; }

.scope-group { margin-bottom: 1.25rem; }
.scope-group:last-child { margin-bottom: 0; }
.scope-type-label { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #1a1a1a; margin-bottom: 0.5rem; }
.type-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.scope-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; padding-left: 1.25rem; }
.scope-list li { display: flex; align-items: flex-start; gap: 0.5rem; color: #333; font-size: 0.9rem; }
.scope-list li:not(.empty-scope)::before { content: '✓'; color: #00805c; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
.scope-list .empty-scope { color: #aaa; font-style: italic; }

.long-desc { color: #444; line-height: 1.8; margin: 0; white-space: pre-wrap; }

.category-badge { display: inline-block; padding: 0.375rem 0.75rem; background: #e6fcf3; color: #00805c; border-radius: 6px; font-size: 0.85rem; font-weight: 500; }
.status-badge { display: inline-block; padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
.status-badge.active { background: #d1fae5; color: #065f46; }
.status-badge.inactive { background: #fee2e2; color: #991b1b; }
.status-badge.draft { background: #f3f4f6; color: #555; }
.featured-badge { display: inline-block; padding: 0.375rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 0.85rem; font-weight: 500; }

.btn-secondary { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; background: #f3f4f6; color: #666; border: none; transition: all 0.3s ease; display: inline-flex; align-items: center; }
.btn-secondary:hover { background: #e5e7eb; color: #1a1a1a; }
.btn-danger { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; border: none; background: #fee2e2; color: #991b1b; transition: all 0.3s ease; }
.btn-danger:hover:not(:disabled) { background: #ef4444; color: white; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem; }
.modal-container { background: white; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h2 { margin: 0; font-size: 1.2rem; color: #1a1a1a; }
.close-btn { background: transparent; border: none; font-size: 1.75rem; color: #666; cursor: pointer; line-height: 1; }
.modal-body { padding: 1.5rem; }
.modal-body p { margin: 0; color: #444; line-height: 1.6; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1.5rem; border-top: 1px solid #e5e7eb; }

@media (max-width: 768px) {
  .service-show-page { padding: 1rem; }
  .service-header-card { flex-direction: column; padding: 1.5rem; }
  .service-image { width: 100%; height: 200px; }
  .page-header { flex-direction: column; align-items: flex-start; }
  .details-grid { grid-template-columns: 1fr; }
}
</style>