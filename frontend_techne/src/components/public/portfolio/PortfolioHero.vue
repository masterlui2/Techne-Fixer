<!-- src/components/public/portfolio/PortfolioHero.vue -->
<template>
  <HeroSection
    title="Our Portfolio"
    description="Discover our successful projects and satisfied clients. Quality workmanship delivered on time, every time."
    button-text="View Projects"
    :image="characterImage"
    image-alt="Techne-Fixer Portfolio"
    @button-click="scrollToProjects"
  >
    <template #extra-content>
      <div class="stats-mini">
        <div class="stat-item">
          <span class="stat-number">150+</span>
          <span class="stat-label">Projects</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">98%</span>
          <span class="stat-label">Satisfaction</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">24/7</span>
          <span class="stat-label">Available</span>
        </div>
      </div>
    </template>
  </HeroSection>
</template>

<script setup>
import HeroSection from '@/components/common/HeroSection.vue';
import characterImage from '@/assets/images/portfolio.png';

const scrollToProjects = () => {
  // Scroll to projects section
};
</script>

<style scoped>
.stats-mini {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: #00ff88;
  line-height: 1;
}

.stat-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 1px;
}

@media (max-width: 900px) {
  .stats-mini {
    justify-content: center;
  }
}

@media (max-width: 600px) {
  .stats-mini {
    gap: 1rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .stat-label {
    font-size: 0.8rem;
  }
}
</style>