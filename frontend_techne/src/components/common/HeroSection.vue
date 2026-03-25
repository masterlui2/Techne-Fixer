<!-- src/components/common/HeroSection.vue -->
<template>
  <section class="hero-section">
    <!-- Floating particles -->
    <div class="particles">
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
    </div>

    <div class="hero-main-content-area">
      <div class="hero-content-wrapper">
        <div class="hero-text-content">
<<<<<<< HEAD:frontend_techne/src/components/public/HeroSection.vue
          <h1>
            Professional Repair Services For Your Electronics & Appliances
          </h1>
          <p>
            Expert technicians specializing in Laptops, Printers, Washing
            Machines, Cellphones, CCTV, Solar Panels & Medical Equipment
          </p>
          <button class="hero-button">
            Learn More <span class="arrow">&gt;</span>
          </button>
        </div>
        <div class="hero-illustration-placeholder">
          <img
            :src="characterImage"
            alt="Kenji 'Spark' Tanaka, Electrical Engineer"
            class="hero-character-image"
          />
=======
          <div class="text-container">
            <h1>{{ title }}</h1>
            <p>{{ description }}</p>
            
            <!-- Optional slots for additional content -->
            <div class="extra-content-wrapper">
              <slot name="extra-content"></slot>
            </div>
            
            <div class="button-wrapper">
              <button v-if="buttonText" class="hero-button" @click="handleButtonClick">
                {{ buttonText }} <span class="arrow">&gt;</span>
              </button>
            </div>
          </div>
        </div>
        <div class="hero-illustration-placeholder">
          <img :src="image" :alt="imageAlt" class="hero-character-image" />
>>>>>>> f08916306df4679e8e7dc9c237cba60e52cccef4:frontend_techne/src/components/common/HeroSection.vue
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
<<<<<<< HEAD:frontend_techne/src/components/public/HeroSection.vue
import characterImage from "@/assets/images/final.png";
=======
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  buttonText: {
    type: String,
    default: ''
  },
  image: {
    type: String,
    required: true
  },
  imageAlt: {
    type: String,
    default: 'Techne-Fixer Service'
  }
});

const emit = defineEmits(['button-click']);

const handleButtonClick = () => {
  emit('button-click');
};
>>>>>>> f08916306df4679e8e7dc9c237cba60e52cccef4:frontend_techne/src/components/common/HeroSection.vue
</script>

<style scoped>
/* Hero Section Background */
.hero-section {
  background: linear-gradient(135deg, #0a1f1a 0%, #0d2820 50%, #0f2d24 100%);
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  width: 100%;
}

/* Floating particles */
.particles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  overflow: hidden;
}

.particle {
  position: absolute;
  width: 12px;
  height: 12px;
  background: #00ff88;
  border-radius: 50%;
  opacity: 0;
  animation: float-up 15s linear infinite;
  box-shadow:
    0 0 20px #00ff88,
    0 0 40px rgba(0, 255, 136, 0.5);
}

.particle:nth-child(1) {
  left: 10%;
  animation-delay: 0s;
  animation-duration: 12s;
}

.particle:nth-child(2) {
  left: 25%;
  animation-delay: 3s;
  animation-duration: 15s;
}

.particle:nth-child(3) {
  left: 40%;
  animation-delay: 1s;
  animation-duration: 18s;
}

.particle:nth-child(4) {
  left: 55%;
  animation-delay: 5s;
  animation-duration: 13s;
}

.particle:nth-child(5) {
  left: 70%;
  animation-delay: 2s;
  animation-duration: 16s;
}

.particle:nth-child(6) {
  left: 80%;
  animation-delay: 7s;
  animation-duration: 14s;
}

.particle:nth-child(7) {
  left: 15%;
  animation-delay: 4s;
  animation-duration: 17s;
}

.particle:nth-child(8) {
  left: 90%;
  animation-delay: 6s;
  animation-duration: 19s;
}

@keyframes float-up {
  0% {
    bottom: -10%;
    opacity: 0;
    transform: translateX(0) scale(0.5);
  }
  10% {
    opacity: 0.8;
  }
  50% {
    transform: translateX(20px) scale(1);
  }
  90% {
    opacity: 0.8;
  }
  100% {
    bottom: 110%;
    opacity: 0;
    transform: translateX(-20px) scale(0.5);
  }
}

.hero-main-content-area {
  flex-grow: 1;
  min-height: calc(100vh - 92px);
  display: flex;
  align-items: center;
  padding: 3rem 0;
  position: relative;
  width: 100%;
}

.hero-content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 3rem;
  width: 100%;
  box-sizing: border-box;
}

.hero-text-content {
  flex: 1;
  text-align: left;
  color: #ffffff;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* NEW: Container to keep consistent positioning */
.text-container {
  display: flex;
  flex-direction: column;
}

.hero-text-content h1 {
  font-size: 3.5em;
  line-height: 1.1;
  margin-bottom: 0.5em;
  font-weight: 800;
  color: #ffffff;
  word-wrap: break-word;
  min-height: 1.1em; /* Keep title height consistent */
}

.hero-text-content p {
  font-size: 1.3em;
  line-height: 1.5;
  margin-bottom: 1.5em;
  color: rgba(255, 255, 255, 0.85);
  word-wrap: break-word;
  min-height: 3em; /* Reserve space for 2 lines minimum */
}

/* NEW: Reserve space for extra content */
.extra-content-wrapper {
  min-height: 120px; /* Reserve consistent space for badges/stats */
  display: flex;
  flex-direction: column;
  margin-bottom: 1em;
}

/* NEW: Keep button at consistent position */
.button-wrapper {
  margin-top: auto;
}

.hero-button {
  background-color: #00ff88;
  color: #0a1f1a;
  border: none;
  padding: 15px 30px;
  font-size: 1.1em;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
}

.hero-button:hover {
  background-color: #00dd77;
  transform: translateX(4px);
  box-shadow: 0 0 40px rgba(0, 255, 136, 0.5);
}

.hero-button .arrow {
  margin-left: 10px;
  font-size: 1.2em;
  transition: transform 0.3s ease;
}

.hero-button:hover .arrow {
  transform: translateX(5px);
}

.hero-illustration-placeholder {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  overflow: hidden;
  max-width: 100%;
}

.hero-character-image {
  max-width: 100%;
  height: auto;
  display: block;
}

/* Responsive adjustments */
@media (max-width: 900px) {
  .particles {
    opacity: 0.5;
  }

  .hero-main-content-area {
    min-height: calc(100vh - 60px);
    padding: 2rem 0;
  }

  .hero-content-wrapper {
    flex-direction: column;
    text-align: center;
    gap: 2rem;
    padding: 0 1.5rem;
  }

  .hero-text-content {
    max-width: 100%;
    text-align: center;
    align-items: center;
  }

  .button-wrapper {
    display: flex;
    justify-content: center;
  }
  
  .extra-content-wrapper {
    min-height: auto; /* Remove fixed height on mobile */
  }

  .hero-illustration-placeholder {
    width: 100%;
    margin-top: 0;
  }

  .hero-character-image {
    max-height: 300px;
    width: auto;
  }

  .hero-text-content h1 {
    font-size: 2.5em;
    min-height: auto;
  }

  .hero-text-content p {
    font-size: 1em;
    min-height: auto;
  }
}

@media (max-width: 600px) {
  .hero-content-wrapper {
    padding: 0 1rem;
  }

  .hero-text-content h1 {
    font-size: 2em;
  }

  .hero-text-content p {
    font-size: 0.95em;
  }

  .hero-button {
    padding: 12px 24px;
    font-size: 1em;
  }

  .hero-character-image {
    max-height: 250px;
  }
}
</style>
