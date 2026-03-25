<template>
  <section class="portfolio-section">
    <div class="portfolio-container">
      <div class="section-header">
        <h2>Our Portfolio</h2>
        <p>Successful projects delivered to clients worldwide</p>
      </div>

      <div v-if="loading" class="feedback-state">
        <div class="spinner"></div>
        <p>Loading portfolio...</p>
      </div>

      <div v-else-if="error" class="feedback-state error">
        <p>{{ error }}</p>
        <button class="btn-retry" @click="fetchPortfolio">Retry</button>
      </div>

      <div v-else-if="projects.length === 0" class="feedback-state">
        <p>No portfolio items yet.</p>
      </div>

      <div v-else class="portfolio-grid">
        <div
          v-for="project in projects"
          :key="project.id"
          class="portfolio-card"
          @click="handleViewProject(project)"
        >
          <!-- Thumbnail -->
          <div class="card-thumbnail">
            <img
              :src="project.thumbnail || 'https://placehold.co/380x220'"
              :alt="project.title"
              class="thumbnail-image"
            />
          </div>

          <div class="card-header">
            <h3>{{ project.title }}</h3>
          </div>

          <div class="card-body">
            <p class="card-description">{{ project.description }}</p>

            <div class="project-services">
              <span
                v-for="(service, sIndex) in project.services"
                :key="sIndex"
                :class="['service-badge', service.color]"
              >
                <span class="badge-dot"></span>
                {{ service.name }}
              </span>
            </div>
          </div>

          <div class="hover-overlay">
            <button class="view-button">View</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const projects = ref([]);
const loading  = ref(true);
const error    = ref(null);

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const fetchPortfolio = async () => {
  loading.value = true;
  error.value   = null;
  try {
    const response = await fetch(`${API_URL}/portfolio`);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const data = await response.json();
    projects.value = data.data;
  } catch (err) {
    error.value = 'Failed to load portfolio. Please try again.';
    console.error('Portfolio fetch error:', err);
  } finally {
    loading.value = false;
  }
};

const handleViewProject = (project) => {
  console.log('View project:', project.title);
};

onMounted(fetchPortfolio);
</script>

<style scoped>
.portfolio-section { background: #f8f9fa; padding: 5rem 0; position: relative; width: 100%; }
.portfolio-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

.section-header { text-align: center; margin-bottom: 3rem; }
.section-header h2 { font-size: 2.5rem; font-weight: 800; color: #1a1a1a; margin-bottom: 0.5rem; }
.section-header p { font-size: 1.2rem; color: #666; }

.feedback-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; text-align: center; }
.feedback-state.error { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.btn-retry:hover { border-color: #00ff88; }
.spinner { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.portfolio-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 2rem; }
.portfolio-card { background: white; border: 2px solid #e0e0e0; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; cursor: pointer; display: flex; flex-direction: column; position: relative; height: 100%; }
.portfolio-card:hover { border-color: #00ff88; transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,0,0,0.15); }
.portfolio-card:hover .hover-overlay { opacity: 1; visibility: visible; }

.card-thumbnail { width: 100%; height: 220px; overflow: hidden; background: #f0f0f0; }
.thumbnail-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
.portfolio-card:hover .thumbnail-image { transform: scale(1.05); }

.card-header { padding: 1.5rem; background: white; border-bottom: 2px solid #e0e0e0; }
.card-header h3 { font-size: 1.25rem; font-weight: 700; color: #1a1a1a; margin: 0; line-height: 1.4; }

.card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.card-description { font-size: 0.95rem; color: #666; line-height: 1.6; margin: 0 0 1.25rem 0; }

.project-services { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; }
.service-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 2px solid; white-space: nowrap; }
.badge-dot { width: 7px; height: 7px; border-radius: 50%; }
.service-badge.yellow { background: #fff9e6; border-color: #ffd93d; color: #b8860b; }
.service-badge.yellow .badge-dot { background: #ffd93d; }
.service-badge.red { background: #ffe6e6; border-color: #ff6b6b; color: #c92a2a; }
.service-badge.red .badge-dot { background: #ff6b6b; }
.service-badge.blue { background: #e6f7ff; border-color: #40a9ff; color: #096dd9; }
.service-badge.blue .badge-dot { background: #40a9ff; }
.service-badge.green { background: #e6fcf3; border-color: #00ff88; color: #00805c; }
.service-badge.green .badge-dot { background: #00ff88; }

.hover-overlay { position: absolute; inset: 0; background: radial-gradient(circle, rgba(10,31,26,0.85) 0%, rgba(10,31,26,0.95) 100%); display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; border-radius: 10px; }
.view-button { padding: 1rem 2.5rem; background: #00ff88; color: #0a1f1a; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,255,136,0.4); text-transform: uppercase; letter-spacing: 0.5px; }
.view-button:hover { background: #00dd77; transform: scale(1.05); }

@media (max-width: 1024px) { .portfolio-grid { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; } }
@media (max-width: 768px) {
  .portfolio-section { padding: 3rem 0; }
  .section-header h2 { font-size: 2rem; }
  .section-header p { font-size: 1rem; }
  .portfolio-grid { grid-template-columns: 1fr; gap: 1.5rem; }
  .card-header { padding: 1.25rem; }
  .card-header h3 { font-size: 1.1rem; }
  .card-body { padding: 1.25rem; }
  .card-description { font-size: 0.9rem; }
  .service-badge { font-size: 0.75rem; padding: 0.35rem 0.75rem; }
}
</style>