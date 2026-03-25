<!-- src/components/public/PortfolioSection.vue -->
<template>
  <section class="portfolio-section">
    <div class="portfolio-container">
      <div class="section-header">
        <h2>Our Work</h2>
        <p>Successful repairs and installations completed for satisfied clients</p>
      </div>

      <div class="view-toggle">
        <button 
          :class="['toggle-btn', { active: viewMode === 'list' }]"
          @click="viewMode = 'list'"
          aria-label="List view"
        >
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <rect x="3" y="4" width="14" height="2" fill="currentColor"/>
            <rect x="3" y="9" width="14" height="2" fill="currentColor"/>
            <rect x="3" y="14" width="14" height="2" fill="currentColor"/>
          </svg>
        </button>
        <button 
          :class="['toggle-btn', { active: viewMode === 'grid' }]"
          @click="viewMode = 'grid'"
          aria-label="Grid view"
        >
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <rect x="3" y="3" width="6" height="6" fill="currentColor"/>
            <rect x="11" y="3" width="6" height="6" fill="currentColor"/>
            <rect x="3" y="11" width="6" height="6" fill="currentColor"/>
            <rect x="11" y="11" width="6" height="6" fill="currentColor"/>
          </svg>
        </button>
      </div>

      <div :class="['portfolio-table', viewMode]">
        <div class="table-header" v-if="viewMode === 'list'">
          <div class="header-cell number">#</div>
          <div class="header-cell project">PROJECT</div>
          <div class="header-cell services">SERVICES</div>
        </div>

        <!-- List View -->
        <div class="table-body" v-if="viewMode === 'list'">
          <div 
            v-for="(project, index) in projects" 
            :key="index"
            :class="['project-row', viewMode]"
          >
            <div class="row-number">{{ String(index + 1).padStart(2, '0') }}</div>
            
            <div class="project-info">
              <h3>{{ project.title }}</h3>
              <p>{{ project.description }}</p>
            </div>
            
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
        </div>

        <!-- Grid View - Carousel on Mobile -->
        <div v-else class="grid-view-wrapper">
          <!-- Desktop/Tablet Grid -->
          <div v-if="!isMobile" class="grid-carousel-container">
            <div class="table-body grid-body desktop-grid">
              <div 
                v-for="(project, index) in projects" 
                :key="index"
                class="project-row grid"
              >
                <div class="project-info">
                  <h3>{{ project.title }}</h3>
                  <p>{{ project.description }}</p>
                </div>
                
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
            </div>
          </div>

          <!-- Mobile Carousel -->
          <div v-else class="mobile-carousel-wrapper">
            <button 
              class="carousel-btn prev" 
              @click="prevSlide" 
              :disabled="currentSlide === 0"
              aria-label="Previous project">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>

            <div class="carousel-track-container">
              <div 
                class="carousel-track"
                :style="carouselStyle"
                @touchstart="handleTouchStart"
                @touchmove="handleTouchMove"
                @touchend="handleTouchEnd"
              >
                <div 
                  v-for="(project, index) in projects" 
                  :key="index"
                  class="project-card-mobile"
                >
                  <div class="project-row grid">
                    <div class="project-info">
                      <h3>{{ project.title }}</h3>
                      <p>{{ project.description }}</p>
                    </div>
                    
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
                </div>
              </div>
            </div>

            <button 
              class="carousel-btn next" 
              @click="nextSlide"
              :disabled="currentSlide >= maxSlide"
              aria-label="Next project">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>

          <div v-if="isMobile" class="carousel-dots">
            <button 
              v-for="(dot, index) in projects.length" 
              :key="index"
              class="dot"
              :class="{ active: index === currentSlide }"
              @click="goToSlide(index)"
              :aria-label="`Go to project ${index + 1}`"
            ></button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const viewMode = ref('list');
const currentSlide = ref(0);
const isMobile = ref(false);
const touchStartX = ref(0);
const touchEndX = ref(0);

const projects = [
  {
    title: 'Corporate Office CCTV Installation',
    description: 'Complete security system installation for a 5-floor office building in Makati, including 24 HD cameras with cloud storage and remote monitoring capabilities.',
    services: [
      { name: 'CCTV Installation', color: 'yellow' },
      { name: 'Network Setup', color: 'blue' }
    ]
  },
  {
    title: 'Medical Clinic Equipment Maintenance',
    description: 'Quarterly maintenance and calibration service for diagnostic equipment at a multi-specialty clinic in Quezon City.',
    services: [
      { name: 'Medical Equipment', color: 'red' },
      { name: 'Preventive Maintenance', color: 'blue' }
    ]
  },
  {
    title: 'Residential Solar Panel Installation',
    description: 'Installation of 10kW solar panel system for a residential property in Alabang, providing clean energy and reducing electricity costs by 70%.',
    services: [
      { name: 'Solar Panel', color: 'yellow' },
      { name: 'Installation', color: 'blue' },
      { name: 'Energy Audit', color: 'red' }
    ]
  },
  {
    title: 'Laundromat Washing Machine Repair',
    description: 'Complete overhaul and repair of 8 commercial washing machines for a laundromat business, including motor replacement and drum balancing.',
    services: [
      { name: 'Washing Machine', color: 'blue' },
      { name: 'Commercial Repair', color: 'yellow' }
    ]
  },
  {
    title: 'IT Company Printer Fleet Maintenance',
    description: 'Monthly maintenance contract for 30+ office printers across 3 locations, ensuring minimal downtime and optimal performance.',
    services: [
      { name: 'Printer Repair', color: 'red' },
      { name: 'Fleet Management', color: 'yellow' },
      { name: 'Parts Replacement', color: 'blue' }
    ]
  },
  {
    title: 'Laptop Repair & Upgrade for SME',
    description: 'Bulk repair and upgrade service for 25 company laptops, including SSD upgrades, RAM expansion, and OS optimization for improved productivity.',
    services: [
      { name: 'Laptop Repair', color: 'yellow' },
      { name: 'Hardware Upgrade', color: 'red' }
    ]
  },
  {
    title: 'Cellphone Repair Center Partnership',
    description: 'Ongoing partnership providing screen replacement, battery services, and software troubleshooting for a phone retail chain.',
    services: [
      { name: 'Cellphone Repair', color: 'blue' },
      { name: 'Bulk Service', color: 'yellow' }
    ]
  }
];

const maxSlide = computed(() => projects.length - 1);

const carouselStyle = computed(() => {
  if (!isMobile.value || viewMode.value !== 'grid') return {};
  const offset = currentSlide.value * 100;
  return {
    transform: `translateX(-${offset}%)`,
    transition: 'transform 0.5s ease'
  };
});

const nextSlide = () => {
  if (currentSlide.value < maxSlide.value) {
    currentSlide.value++;
  }
};

const prevSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--;
  }
};

const goToSlide = (index) => {
  currentSlide.value = index;
};

// Touch handlers for mobile swipe
const handleTouchStart = (e) => {
  touchStartX.value = e.touches[0].clientX;
};

const handleTouchMove = (e) => {
  touchEndX.value = e.touches[0].clientX;
};

const handleTouchEnd = () => {
  const swipeThreshold = 50;
  const diff = touchStartX.value - touchEndX.value;
  
  if (Math.abs(diff) > swipeThreshold) {
    if (diff > 0) {
      nextSlide();
    } else {
      prevSlide();
    }
  }
  
  touchStartX.value = 0;
  touchEndX.value = 0;
};

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
  if (!isMobile.value) {
    currentSlide.value = 0;
  }
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile);
});
</script>

<style scoped>
.portfolio-section {
  background: #f8f9fa;
  padding: 5rem 0;
  position: relative;
  width: 100%;
  overflow: hidden;
}

.portfolio-container {
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

.view-toggle {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.toggle-btn {
  background: white;
  border: 2px solid #e0e0e0;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #666;
}

.toggle-btn:hover {
  border-color: #00ff88;
  color: #00ff88;
}

.toggle-btn.active {
  background: #00ff88;
  border-color: #00ff88;
  color: #0a1f1a;
}

.portfolio-table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table-header {
  display: grid;
  grid-template-columns: 60px 1fr 1fr;
  gap: 2rem;
  padding: 1.5rem 2rem;
  background: #f8f9fa;
  border-bottom: 2px solid #e0e0e0;
  font-weight: 700;
  font-size: 0.75rem;
  color: #666;
  letter-spacing: 0.5px;
}

.table-body {
  display: flex;
  flex-direction: column;
}

.project-row.list {
  display: grid;
  grid-template-columns: 60px 1fr 1fr;
  gap: 2rem;
  padding: 2rem;
  border-bottom: 1px solid #e0e0e0;
  transition: all 0.3s ease;
  align-items: start;
}

.project-row.list:hover {
  background: #f8f9fa;
}

.project-row.list:last-child {
  border-bottom: none;
}

.row-number {
  font-size: 1.2rem;
  font-weight: 700;
  color: #00ff88;
  line-height: 1;
}

.project-info h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
  word-wrap: break-word;
  line-height: 1.4;
}

.project-info p {
  font-size: 0.95rem;
  color: #666;
  line-height: 1.6;
  word-wrap: break-word;
}

.project-services {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: flex-start;
}

.service-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 2px solid;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.service-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.badge-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.service-badge.yellow {
  background: #fff9e6;
  border-color: #ffd93d;
  color: #b8860b;
}

.service-badge.yellow .badge-dot {
  background: #ffd93d;
}

.service-badge.red {
  background: #ffe6e6;
  border-color: #ff6b6b;
  color: #c92a2a;
}

.service-badge.red .badge-dot {
  background: #ff6b6b;
}

.service-badge.blue {
  background: #e6f7ff;
  border-color: #40a9ff;
  color: #096dd9;
}

.service-badge.blue .badge-dot {
  background: #40a9ff;
}

/* Grid View */
.grid-view-wrapper {
  position: relative;
}

.grid-carousel-container {
  overflow: hidden;
  width: 100%;
}

.table-body.grid-body.desktop-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  padding: 2rem;
}

.project-row.grid {
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 2rem;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  min-height: 240px;
  max-height: 320px;
}

.project-row.grid:hover {
  border-color: #00ff88;
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.project-row.grid .project-info {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 0.5rem;
}

.project-row.grid .project-info::-webkit-scrollbar {
  width: 4px;
}

.project-row.grid .project-info::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.project-row.grid .project-info::-webkit-scrollbar-thumb {
  background: #00ff88;
  border-radius: 10px;
}

.project-row.grid .project-info::-webkit-scrollbar-thumb:hover {
  background: #00dd77;
}

.project-row.grid .project-services {
  flex-shrink: 0;
}

/* Mobile Carousel Structure */
.mobile-carousel-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  background: transparent;
}

.carousel-track-container {
  overflow: hidden;
  flex: 1;
  background: transparent;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease;
  user-select: none;
  background: transparent;
}

.project-card-mobile {
  flex: 0 0 100%;
  min-width: 100%;
  padding: 0 0.5rem;
  box-sizing: border-box;
}

/* Carousel Buttons */
.carousel-btn {
  display: none;
  background: #00ff88;
  border: none;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #0a1f1a;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 255, 136, 0.3);
}

.carousel-btn:hover:not(:disabled) {
  background: #00dd77;
  transform: scale(1.1);
}

.carousel-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
  box-shadow: none;
}

.carousel-dots {
  display: none;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
  padding: 0 1rem;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #ddd;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
}

.dot.active {
  background: #00ff88;
  width: 28px;
  border-radius: 5px;
}

.dot:hover {
  background: #00ff88;
  opacity: 0.7;
}

/* Mobile Carousel Structure (like Services) */
.mobile-carousel-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  background: transparent;
}

.carousel-track-container {
  overflow: hidden;
  flex: 1;
  background: transparent;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease;
  user-select: none;
  background: transparent;
}

.project-card-mobile {
  flex: 0 0 100%;
  min-width: 100%;
  padding: 0 0.5rem;
  box-sizing: border-box;
}

/* Carousel Buttons */
.carousel-btn {
  display: none;
  background: #00ff88;
  border: none;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #0a1f1a;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 255, 136, 0.3);
}

.carousel-btn:hover:not(:disabled) {
  background: #00dd77;
  transform: scale(1.1);
}

.carousel-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
  box-shadow: none;
}

.carousel-dots {
  display: none;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
  padding: 0 1rem;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #ddd;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
}

.dot.active {
  background: #00ff88;
  width: 28px;
  border-radius: 5px;
}

.dot:hover {
  background: #00ff88;
  opacity: 0.7;
}

/* Responsive */
@media (max-width: 1024px) {
  .portfolio-container {
    padding: 0 1.5rem;
  }
  
  .table-header {
    grid-template-columns: 50px 1fr 1fr;
    gap: 1.5rem;
    padding: 1.25rem 1.5rem;
  }
  
  .project-row.list {
    grid-template-columns: 50px 1fr 1fr;
    gap: 1.5rem;
    padding: 1.5rem;
  }
  
  .portfolio-table.grid .table-body {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 768px) {
  .portfolio-section {
    padding: 3rem 0;
  }
  
  .portfolio-container {
    padding: 0 1rem;
  }
  
  .section-header {
    margin-bottom: 2rem;
  }
  
  .section-header h2 {
    font-size: 1.8rem;
  }
  
  .section-header p {
    font-size: 1rem;
  }
  
  .view-toggle {
    margin-bottom: 1.5rem;
  }
  
  .table-header {
    display: none;
  }
  
  /* List View Mobile */
  .project-row.list {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 1.5rem 1rem;
    position: relative;
    padding-top: 1rem;
  }
  
  .row-number {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1rem;
  }
  
  .project-info {
    margin-bottom: 0.75rem;
  }
  
  .project-info h3 {
    font-size: 1rem;
    margin-bottom: 0.4rem;
    padding-right: 2.5rem;
  }
  
  .project-info p {
    font-size: 0.875rem;
    line-height: 1.5;
  }
  
  .project-services {
    gap: 0.4rem;
  }
  
  /* Grid View Mobile - Carousel Mode */
  .carousel-btn {
    display: flex;
    width: 44px;
    height: 44px;
  }
  
  .carousel-btn svg {
    width: 22px;
    height: 22px;
  }
  
  .portfolio-table.grid {
    background: transparent;
    box-shadow: none;
  }
  
  .project-card-mobile .project-row.grid {
    padding: 1.75rem 1.25rem;
    min-height: 320px;
    max-height: 400px;
  }
  
  .project-card-mobile .project-info {
    overflow-y: auto;
    padding-right: 0.25rem;
  }
  
  .project-card-mobile .project-info h3 {
    font-size: 1.05rem;
    line-height: 1.3;
    margin-bottom: 0.5rem;
    flex-shrink: 0;
  }
  
  .project-card-mobile .project-info p {
    font-size: 0.875rem;
    line-height: 1.5;
  }
  
  .project-card-mobile .project-services {
    margin-top: auto;
    padding-top: 1rem;
    flex-shrink: 0;
  }
  
  .project-row.grid:hover {
    transform: none;
  }
  
  .carousel-dots {
    display: flex;
  }
  
  .service-badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    gap: 0.375rem;
  }
  
  .badge-dot {
    width: 6px;
    height: 6px;
  }
}

@media (max-width: 480px) {
  .portfolio-section {
    padding: 2.5rem 0;
  }
  
  .portfolio-container {
    padding: 0 0.75rem;
  }
  
  .section-header h2 {
    font-size: 1.5rem;
  }
  
  .section-header p {
    font-size: 0.9rem;
  }
  
  .view-toggle {
    gap: 0.375rem;
  }
  
  .toggle-btn {
    width: 36px;
    height: 36px;
  }
  
  .toggle-btn svg {
    width: 18px;
    height: 18px;
  }
  
  /* List View */
  .project-row.list {
    padding: 1.25rem 0.875rem;
  }
  
  .row-number {
    font-size: 0.9rem;
    top: 0.875rem;
    right: 0.875rem;
  }
  
  .project-info h3 {
    font-size: 0.95rem;
    padding-right: 2rem;
  }
  
  .project-info p {
    font-size: 0.825rem;
  }
  
  /* Grid View Carousel */
  .mobile-carousel-wrapper {
    gap: 0.375rem;
  }
  
  .carousel-btn {
    width: 40px;
    height: 40px;
  }
  
  .carousel-btn svg {
    width: 20px;
    height: 20px;
  }
  
  .project-card-mobile {
    padding: 0 0.375rem;
  }
  
  .project-card-mobile .project-row.grid {
    padding: 1.5rem 1rem;
    min-height: 300px;
    max-height: 380px;
  }
  
  .project-card-mobile .project-info h3 {
    font-size: 0.95rem;
  }
  
  .project-card-mobile .project-info p {
    font-size: 0.825rem;
  }
  
  .service-badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
  }
  
  .portfolio-table {
    border-radius: 8px;
  }
  
  .dot {
    width: 8px;
    height: 8px;
  }
  
  .dot.active {
    width: 24px;
  }
}

@media (max-width: 360px) {
  .section-header h2 {
    font-size: 1.35rem;
  }
  
  .project-info h3 {
    font-size: 0.9rem;
  }
  
  .project-info p {
    font-size: 0.8rem;
  }
  
  .service-badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
  }
}
</style>