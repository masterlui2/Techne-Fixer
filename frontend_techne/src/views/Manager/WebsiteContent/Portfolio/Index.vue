<template>
  <div class="portfolio-page">

    <div class="page-header">
      <div class="header-content">
        <h1>Manage Portfolio</h1>
        <p class="subtitle">Create and manage your portfolio projects</p>
      </div>
      <router-link to="/admin/portfolio/create" class="btn-primary">+ Add Project</router-link>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Total" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ projects.length }}</div>
          <div class="stat-label">Total Projects</div>
        </div>
      </div>
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Repair" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ countByService('Repair') }}</div>
          <div class="stat-label">Repair Projects</div>
        </div>
      </div>
      <div class="stat-card">
        <img src="https://placehold.co/60x60" alt="Installation" class="stat-img" />
        <div class="stat-content">
          <div class="stat-value">{{ countByService('Installation') }}</div>
          <div class="stat-label">Installation Projects</div>
        </div>
      </div>
    </div>

    <div class="table-container">
      <div class="table-header">
        <input type="text" v-model="searchQuery" placeholder="Search projects..." class="search-input" />
        <select v-model="filterService" class="filter-select">
          <option value="all">All Services</option>
          <option value="Repair">Repair</option>
          <option value="Installation">Installation</option>
          <option value="Maintenance">Maintenance</option>
          <option value="Upgrade">Upgrade</option>
        </select>
      </div>

      <div v-if="loading" class="feedback-state">
        <div class="spinner-lg"></div>
        <p>Loading projects...</p>
      </div>

      <div v-else-if="error" class="feedback-state error">
        <p>{{ error }}</p>
        <button class="btn-retry" @click="fetchProjects">Retry</button>
      </div>

      <div v-else class="table-wrapper">
        <table class="portfolio-table">
          <thead>
            <tr>
              <th>Project</th>
              <th>Services</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in filteredProjects" :key="project.id">
              <td>
                <div class="project-info">
                  <img
                    :src="project.thumbnail ? `${baseURL}/storage/${project.thumbnail}` : 'https://placehold.co/80x60'"
                    :alt="project.title"
                    class="project-thumb"
                  />
                  <div>
                    <div class="project-title">{{ project.title }}</div>
                    <div class="project-desc">{{ truncate(project.description, 60) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="badges">
                  <span v-for="(service, index) in project.services" :key="index" class="service-badge" :class="service.color">
                    <span class="badge-dot"></span>
                    {{ service.name }}
                  </span>
                </div>
              </td>
              <td>
                <span class="status-badge" :class="project.status">{{ capitalize(project.status) }}</span>
              </td>
              <td><span class="date">{{ formatDate(project.created_at) }}</span></td>
              <td>
                <div class="action-buttons">
                  <router-link :to="`/admin/portfolio/${project.id}`" class="btn-icon btn-view">View</router-link>
                  <router-link :to="`/admin/portfolio/${project.id}/edit`" class="btn-icon btn-edit">Edit</router-link>
                  <button class="btn-icon btn-delete" @click="confirmDelete(project)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredProjects.length === 0" class="empty-state">
          <img src="https://placehold.co/120x120" alt="No projects" class="empty-img" />
          <h3>No projects found</h3>
          <p>{{ searchQuery ? 'Try adjusting your search' : 'Get started by adding your first project' }}</p>
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
          <p>Are you sure you want to delete <strong>{{ projectToDelete?.title }}</strong>? This action cannot be undone.</p>
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
import { ref, computed, onMounted } from 'vue';
import axios, { baseURL } from '@/api/axios';

const projects        = ref([]);
const loading         = ref(false);
const error           = ref(null);
const isDeleting      = ref(false);
const searchQuery     = ref('');
const filterService   = ref('all');
const showDeleteModal = ref(false);
const projectToDelete = ref(null);

// ── Computed ──────────────────────────────────────────────
const filteredProjects = computed(() => {
  return projects.value.filter(p => {
    const matchesSearch = !searchQuery.value
      || p.title.toLowerCase().includes(searchQuery.value.toLowerCase())
      || p.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesService = filterService.value === 'all'
      || p.services?.some(s => s.name === filterService.value);
    return matchesSearch && matchesService;
  });
});

const countByService = (name) =>
  projects.value.filter(p => p.services?.some(s => s.name === name)).length;

// ── Helpers ───────────────────────────────────────────────
const truncate   = (text, len) => text?.length > len ? text.substring(0, len) + '...' : (text ?? '');
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : '—';
const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

// ── Fetch ─────────────────────────────────────────────────
const fetchProjects = async () => {
  loading.value = true;
  error.value   = null;
  try {
    const { data } = await axios.get('/admin/portfolio');
    projects.value = data.data;
  } catch (err) {
    error.value = 'Failed to load projects. Please try again.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// ── Delete ────────────────────────────────────────────────
const confirmDelete = (project) => {
  projectToDelete.value = project;
  showDeleteModal.value = true;
};

const deleteProject = async () => {
  isDeleting.value = true;
  try {
    await axios.delete(`/admin/portfolio/${projectToDelete.value.id}`);
    projects.value    = projects.value.filter(p => p.id !== projectToDelete.value.id);
    showDeleteModal.value = false;
    projectToDelete.value = null;
  } catch (err) {
    console.error('Failed to delete project:', err);
  } finally {
    isDeleting.value = false;
  }
};

onMounted(fetchProjects);
</script>

<style scoped>
.portfolio-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.header-content h1 { font-size: 2rem; color: #1a1a1a; margin: 0 0 0.25rem 0; }
.subtitle { color: #666; margin: 0; font-size: 0.95rem; }

.btn-primary { background: linear-gradient(135deg, #00ff88 0%, #00cc6f 100%); color: #0a1f1a; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,255,136,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,255,136,0.4); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.3s ease; }
.stat-card:hover { transform: translateY(-4px); }
.stat-img { width: 60px; height: 60px; object-fit: contain; border-radius: 12px; }
.stat-value { font-size: 2rem; font-weight: 700; color: #1a1a1a; line-height: 1; margin-bottom: 0.25rem; }
.stat-label { color: #666; font-size: 0.9rem; }

.table-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.table-header { padding: 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; gap: 1rem; flex-wrap: wrap; }
.search-input { flex: 1; min-width: 200px; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; }
.filter-select { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; background: white; cursor: pointer; }
.search-input:focus, .filter-select:focus { outline: none; border-color: #00ff88; }

.feedback-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; }
.feedback-state.error { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.btn-retry:hover { border-color: #00ff88; }
.spinner-lg { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.table-wrapper { overflow-x: auto; }
.portfolio-table { width: 100%; border-collapse: collapse; }
.portfolio-table thead { background: #f9fafb; }
.portfolio-table th { padding: 1rem 1.5rem; text-align: left; font-weight: 600; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; }
.portfolio-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.portfolio-table tbody tr:hover { background: #f9fafb; }

.project-info { display: flex; align-items: center; gap: 1rem; }
.project-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
.project-title { font-weight: 600; color: #1a1a1a; margin-bottom: 0.25rem; font-size: 0.95rem; }
.project-desc { font-size: 0.85rem; color: #666; }

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
.btn-secondary { padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; background: #f3f4f6; color: #666; border: none; }
.btn-secondary:hover { background: #e5e7eb; color: #1a1a1a; }
.btn-danger { padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; background: #ef4444; color: white; transition: all 0.2s ease; }
.btn-danger:hover:not(:disabled) { background: #dc2626; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 768px) {
  .portfolio-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: flex-start; }
  .stats-grid { grid-template-columns: 1fr; }
  .table-header { flex-direction: column; }
  .search-input { min-width: 100%; }
}
</style>