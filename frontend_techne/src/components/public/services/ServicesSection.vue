<!-- src/components/public/services/ServicesSection.vue -->
<template>
  <section class="services-section">
    <div class="container">
      <div class="section-header">
        <h2>Our Services</h2>
        <p>Professional repair and maintenance for all your equipment</p>
      </div>
      
      <!-- Loading state -->
      <div v-if="loading" class="loading-state">
        <p>Loading services...</p>
      </div>
      
      <!-- Error state -->
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
      </div>
      
      <!-- Services grid -->
      <div v-else class="services-grid">
        <ProjectCard
          v-for="project in projects"
          :key="project.id"
          :category="project.category"
          :name="project.name"
          :description="project.description"
          :services="project.services"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ProjectCard from '@/components/common/cards/ProjectCard.vue';

const projects = ref([]);
const loading = ref(true);
const error = ref(null);

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const fetchServices = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await fetch(`${API_URL}/services`);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    projects.value = data.data;
  } catch (err) {
    error.value = `Failed to fetch services: ${err.message}`;
    console.error('Error fetching services:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchServices();
});
</script>

<style scoped>
.services-section {
  background: #f8f9fa;
  padding: 5rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
}

.section-header p {
  font-size: 1.2rem;
  color: #666;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
}

/* Loading and error states */
.loading-state,
.error-state {
  text-align: center;
  padding: 3rem;
  font-size: 1.1rem;
}

.loading-state {
  color: #666;
}

.error-state {
  color: #dc3545;
}

@media (max-width: 1024px) {
  .services-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .services-section {
    padding: 3rem 0;
  }
  
  .section-header h2 {
    font-size: 2rem;
  }
  
  .section-header p {
    font-size: 1rem;
  }
  
  .services-grid {
    gap: 1.5rem;
  }
}
</style>