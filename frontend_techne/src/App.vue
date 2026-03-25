<!-- src/App.vue -->
<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import PublicLayout from '@/layouts/PublicLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';

const route = useRoute();

const layout = computed(() => {
  if (route.path.startsWith('/admin'))      return AdminLayout;
  if (route.path.startsWith('/technician')) return TechnicianLayout;
  return PublicLayout;
});
</script>

<style>
/* Global Reset and Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  width: 100%;
  overflow-x: hidden;
  scroll-behavior: smooth;
}

body {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

#app {
  width: 100%;
  min-height: 100vh;
  overflow-x: hidden;
}

.router-view {
  width: 100%;
}

button {
  font-family: inherit;
}

a {
  color: inherit;
}
</style>