<!-- src/components/admin/Breadcrumb.vue -->
<template>
  <nav class="breadcrumb">
    <router-link to="/admin" class="breadcrumb-item">
      <span class="breadcrumb-icon">🏠</span>
      Dashboard
    </router-link>
    <span class="breadcrumb-separator">/</span>
    <router-link 
      v-for="(crumb, index) in crumbs" 
      :key="index"
      :to="crumb.path"
      class="breadcrumb-item"
      :class="{ 'active': index === crumbs.length - 1 }">
      {{ crumb.label }}
    </router-link>
  </nav>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  crumbs: {
    type: Array,
    required: true,
    // Example: [{ label: 'Services', path: '/admin/services' }, { label: 'Add Service', path: '/admin/services/add' }]
  }
});
</script>

<style scoped>
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 0;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.breadcrumb-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.3s ease;
}

.breadcrumb-item:hover:not(.active) {
  color: #00ff88;
}

.breadcrumb-item.active {
  color: #1a1a1a;
  font-weight: 600;
  pointer-events: none;
}

.breadcrumb-icon {
  font-size: 1rem;
}

.breadcrumb-separator {
  color: #ccc;
  font-weight: 300;
}
</style>