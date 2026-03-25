<!-- src/layouts/AdminLayout.vue -->
<template>
  <div class="technician-layout">
    <TechnicianSidebar />
    <main class="technician-main">
      <slot />
    </main>
  </div>
</template>

<script setup>
import TechnicianSidebar from '@/components/common/TechnicianSidebar.vue';
</script>

<style scoped>
.technician-layout {
  display: flex;
  min-height: 100vh;
  background: #f5f7fa;
}

.technician-main {
  flex: 1;
  margin-left: 260px;
  padding: 2rem;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .technician-main {
    margin-left: 70px;
    padding: 1rem;
  }
}
</style>