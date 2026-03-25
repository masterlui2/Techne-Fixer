<!-- src/components/public/services/ServicesHero.vue -->
<template>
  <HeroSection
    title="Our Services"
    description="Professional repair and maintenance solutions for all your hardware needs. From air conditioning to CCTV systems, we've got you covered."
    button-text="Get a Quote"
    :image="characterImage"
    image-alt="Techne-Fixer Services"
    @button-click="handleGetQuote"
  />
</template>

<script setup>
import HeroSection from '@/components/common/HeroSection.vue';
import characterImage from '@/assets/images/service.png';

const handleGetQuote = () => {
  // Handle get quote action - scroll to contact form or open quote modal
  console.log('Get a Quote clicked');
};
</script>