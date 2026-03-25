<!-- src/layouts/AdminLayout.vue -->
<template>
  <div class="admin-layout">
    <AdminSidebar />
    <main class="admin-main">
      <slot />
    </main>
  </div>
</template>

<script setup>
import AdminSidebar from '@/components/common/Sidebar.vue';
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f5f7fa;
}

.admin-main {
  flex: 1;
  margin-left: 260px;
  padding: 2rem;
  transition: margin-left 0.3s ease;
  min-height: 100vh;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .admin-main {
    margin-left: 70px;
    padding: 1rem;
  }
}
</style>