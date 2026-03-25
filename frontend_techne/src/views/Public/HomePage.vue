<!-- src/views/HomePage.vue -->
<template>
  <div class="home-page">
    <HeroSection />
    <!-- Now using the separated HeroSection component -->
    <ServiceSection />
    <!-- Now using the separated ServiceSection component -->
    <PortfolioSection />
    <!-- Now using the separated PortfolioSection component -->
    <TestimonialsSection />
    <!-- Now using the separated TestimonialsSection component -->
     <AboutUsSection />
    <!-- Now using the separated AboutUs component -->
    <ContactUsSection />
    <!-- Now using the separated ContactUsSection component -->
  </div>
</template>

<script setup>
import HeroSection from "@/components/public/home/HeroSection.vue"; // <--- Import the new component
import ServiceSection from "@/components/public/home/ServicesCarousel.vue"; // <--- Import the new component
import PortfolioSection from "@/components/public/home/PortfolioSection.vue"; // <--- Import the new component
import TestimonialsSection from "@/components/public/home/TestimonialsSection.vue"; // <--- Import the new component
import ContactUsSection from "@/components/public/ContactUsSection.vue"; // <--- Import the new component
import AboutUsSection from "@/components/public/home/AboutUs.vue"; // <--- Import the new component
</script>

<style scoped>
.home-page {
  width: 100%;
  overflow-x: hidden;
}
</style>
