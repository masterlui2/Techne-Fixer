<!-- src/components/public/about/AboutHero.vue -->
<template>
  <HeroSection
    title="About Us"
    description="Your trusted hardware repair and maintenance partner. Built on expertise, driven by quality, and committed to your satisfaction."
    button-text="Our Story"
    :image="characterImage"
    image-alt="About Techne-Fixer"
    @button-click="scrollToStory"
  >
    <template #extra-content>
      <div class="timeline-badge">
        <span class="badge-icon">📅</span>
        <div class="badge-content">
          <span class="badge-title">Established</span>
          <span class="badge-value">December 2023</span>
        </div>
      </div>
      <div class="values-list">
        <div class="value-item">
          <span class="value-icon">✓</span>
          <span>Quality Service</span>
        </div>
        <div class="value-item">
          <span class="value-icon">✓</span>
          <span>Expert Team</span>
        </div>
        <div class="value-item">
          <span class="value-icon">✓</span>
          <span>24/7 Available</span>
        </div>
      </div>
    </template>
  </HeroSection>
</template>

<script setup>
import HeroSection from '@/components/common/HeroSection.vue';
import characterImage from '@/assets/images/about.png';

const scrollToStory = () => {
  // Scroll to story section
};
</script>

<style scoped>
.timeline-badge {
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  background: rgba(0, 255, 136, 0.1);
  border: 2px solid #00ff88;
  border-radius: 50px;
  padding: 0.8rem 1.5rem;
  margin-bottom: 1.5rem;
}

.badge-icon {
  font-size: 1.5rem;
}

.badge-content {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.badge-title {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.badge-value {
  font-size: 1rem;
  font-weight: 700;
  color: #00ff88;
}

.values-list {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-bottom: 2rem;
}

.value-item {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
}

.value-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  background: #00ff88;
  color: #0a1f1a;
  border-radius: 50%;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}
</style>