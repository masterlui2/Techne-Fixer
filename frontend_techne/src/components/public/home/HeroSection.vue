<!-- src/components/public/HeroSection.vue -->
<template>
  <HeroSection
    title="Professional Repair Services For Your Electronics & Appliances"
    description="Expert technicians specializing in Laptops, Printers, Washing Machines, Cellphones, CCTV, Solar Panels & Medical Equipment"
    button-text="Inquire Now"
    :image="characterImage"
    image-alt="Kenji 'Spark' Tanaka, Electrical Engineer"
    @button-click="handleLearnMore"
  />
</template>

<script setup>
import { useRouter } from 'vue-router';
import HeroSection from '@/components/common/HeroSection.vue';
import characterImage from '@/assets/images/character.png';

const router = useRouter();

const handleLearnMore = () => {
  router.push({ name: 'Inquire' });
};
</script>