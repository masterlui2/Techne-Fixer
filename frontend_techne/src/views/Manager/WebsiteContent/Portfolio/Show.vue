<template>
  <div class="portfolio-show-page">

    <div class="page-header">
      <router-link to="/admin/portfolio" class="back-link">← Back to Portfolio</router-link>
      <div class="header-actions" v-if="!isLoading && project">
        <router-link :to="`/admin/portfolio/${project.id}/edit`" class="btn-secondary">Edit</router-link>
        <button class="btn-danger" @click="showDeleteModal = true">Delete</button>
      </div>
    </div>

    <div v-if="isLoading" class="loading-state">
      <div class="spinner-lg"></div>
      <p>Loading project...</p>
    </div>

    <div v-else-if="loadError" class="error-state">
      <p>{{ loadError }}</p>
      <button class="btn-retry" @click="fetchProject">Retry</button>
    </div>

    <div v-else class="show-container">

      <div class="project-header-card">
        <img
          :src="project.thumbnail ? `${baseURL}/storage/${project.thumbnail}` : 'https://placehold.co/800x400'"
          :alt="project.title"
          class="project-thumbnail"
        />
        <div class="project-header-info">
          <div class="top-row">
            <div class="service-badges">
              <span v-for="(service, index) in project.services" :key="index" class="service-badge" :class="service.color">
                <span class="badge-dot"></span>
                {{ service.name }}
              </span>
            </div>
            <span class="status-badge" :class="project.status">{{ capitalize(project.status) }}</span>
          </div>
          <h1>{{ project.title }}</h1>
          <p class="project-description">{{ project.description }}</p>
          <div class="project-meta">
            <span class="meta-item">Created: {{ formatDate(project.created_at) }}</span>
            <span class="meta-item">Updated: {{ formatDate(project.updated_at) }}</span>
            <span class="meta-item">Order: {{ project.order }}</span>
          </div>
        </div>
      </div>

      <div class="detail-card">
        <h2>Project Details</h2>
        <div class="detail-list">
          <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="status-badge" :class="project.status">{{ capitalize(project.status) }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Services</span>
            <div class="badges">
              <span v-for="(service, index) in project.services" :key="index" class="service-badge" :class="service.color">
                <span class="badge-dot"></span>
                {{ service.name }}
              </span>
            </div>
          </div>
          <div class="detail-item">
            <span class="detail-label">Display Order</span>
            <span class="detail-value">{{ project.order }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Created</span>
            <span class="detail-value">{{ formatDate(project.created_at) }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Last Updated</span>
            <span class="detail-value">{{ formatDate(project.updated_at) }}</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
      <div class="modal-container">
        <div class="modal-header">
          <h2>Delete Project</h2>
          <button class="close-btn" @click="showDeleteModal = false">×</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong>{{ project?.title }}</strong>? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button class="btn-secondary" @click="showDeleteModal = false">Cancel</button>
          <button class="btn-danger" :disabled="isDeleting" @click="deleteProject">
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
import axios, { baseURL } from '@/api/axios';

const router = useRouter();
const route  = useRoute();

const project         = ref(null);
const isLoading       = ref(true);
const loadError       = ref(null);
const isDeleting      = ref(false);
const showDeleteModal = ref(false);

const fetchProject = async () => {
  isLoading.value = true;
  loadError.value = null;
  try {
    const { data } = await axios.get(`/admin/portfolio/${route.params.id}`);
    project.value = data.data;
  } catch (err) {
    loadError.value = 'Failed to load project. Please try again.';
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const deleteProject = async () => {
  isDeleting.value = true;
  try {
    await axios.delete(`/admin/portfolio/${route.params.id}`);
    router.push('/admin/portfolio');
  } catch (err) {
    console.error('Failed to delete project:', err);
    showDeleteModal.value = false;
  } finally {
    isDeleting.value = false;
  }
};

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
const formatDate = (date) => date
  ? new Date(date).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
  : '—';

onMounted(fetchProject);
</script>

<style scoped>
.portfolio-show-page { padding: 2rem; max-width: 1100px; margin: 0 auto; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.back-link { color: #666; text-decoration: none; font-size: 0.9rem; transition: color 0.2s ease; }
.back-link:hover { color: #00805c; }
.header-actions { display: flex; gap: 0.75rem; }

.loading-state, .error-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; text-align: center; }
.error-state { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.spinner-lg { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.show-container { display: flex; flex-direction: column; gap: 1.5rem; }

.project-header-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.project-thumbnail { width: 100%; height: 300px; object-fit: cover; display: block; }
.project-header-info { padding: 2rem; }
.top-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; }
.service-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.project-header-info h1 { font-size: 1.75rem; color: #1a1a1a; margin: 0 0 1rem 0; }
.project-description { color: #666; line-height: 1.6; margin: 0 0 1.5rem 0; }
.project-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
.meta-item { font-size: 0.9rem; color: #999; }

.detail-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem; }
.detail-card h2 { font-size: 1rem; font-weight: 600; color: #1a1a1a; margin: 0 0 1.25rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6; }
.detail-list { display: flex; flex-direction: column; gap: 1rem; }
.detail-item { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
.detail-label { font-size: 0.9rem; color: #666; }
.detail-value { font-weight: 500; color: #1a1a1a; font-size: 0.95rem; }

.badges { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.service-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1.5px solid; }
.badge-dot { width: 6px; height: 6px; border-radius: 50%; }
.service-badge.yellow { background: #fff9e6; border-color: #ffd93d; color: #b8860b; }
.service-badge.yellow .badge-dot { background: #ffd93d; }
.service-badge.red { background: #ffe6e6; border-color: #ff6b6b; color: #c92a2a; }
.service-badge.red .badge-dot { background: #ff6b6b; }
.service-badge.blue { background: #e6f7ff; border-color: #40a9ff; color: #096dd9; }
.service-badge.blue .badge-dot { background: #40a9ff; }
.service-badge.green { background: #e6fcf3; border-color: #00ff88; color: #00805c; }
.service-badge.green .badge-dot { background: #00ff88; }

.status-badge { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.82rem; font-weight: 500; }
.status-badge.published { background: #d1fae5; color: #065f46; }
.status-badge.draft { background: #f3f4f6; color: #555; }

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
  .portfolio-show-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: flex-start; }
  .project-thumbnail { height: 200px; }
  .project-header-info { padding: 1.5rem; }
  .project-meta { flex-direction: column; gap: 0.5rem; }
}
</style>