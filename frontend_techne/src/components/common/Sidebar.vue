<!-- src/components/admin/AdminSidebar.vue -->
<template>
  <aside class="admin-sidebar" :class="{ 'collapsed': isCollapsed }">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <img :src="logo" alt="Techne-Fixer Logo" class="sidebar-logo" />
        <span class="brand-text" v-if="!isCollapsed">Admin Panel</span>
      </div>
      <button 
        class="collapse-btn" 
        @click="toggleSidebar"
        aria-label="Toggle sidebar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline :points="isCollapsed ? '9 18 15 12 9 6' : '15 18 9 12 15 6'"></polyline>
        </svg>
      </button>
    </div>

    <!-- Navigation Links -->
    <nav class="sidebar-nav">
      <router-link to="/admin" class="nav-item" exact-active-class="active">
        <span class="nav-icon">📊</span>
        <span class="nav-text" v-if="!isCollapsed">Dashboard</span>
      </router-link>

      <router-link to="/admin/services" class="nav-item">
        <span class="nav-icon">🔧</span>
        <span class="nav-text" v-if="!isCollapsed">Services</span>
      </router-link>

      <router-link to="/admin/portfolio" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Portfolio</span>
      </router-link>
      
      <router-link to="/admin/inquiry" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Inquiries</span>
      </router-link>
      
      <router-link to="/admin/quotation" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Quotatiton</span>
      </router-link>
      
      <router-link to="/admin/job-order" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Job Order</span>
      </router-link>
      
      <router-link to="/admin/technician" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Technician</span>
      </router-link>

      <router-link to="/admin/customer" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Customer</span>
      </router-link>

      <router-link to="/admin/report" class="nav-item">
        <span class="nav-icon">💼</span>
        <span class="nav-text" v-if="!isCollapsed">Reports</span>
      </router-link>

      <div class="nav-divider" v-if="!isCollapsed"></div>

      <router-link to="/" class="nav-item">
        <span class="nav-icon">🏠</span>
        <span class="nav-text" v-if="!isCollapsed">View Site</span>
      </router-link>

      <a @click="handleLogout" class="nav-item logout-item">
        <span class="nav-icon">🚪</span>
        <span class="nav-text" v-if="!isCollapsed">Logout</span>
      </a>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import {useAuthStore} from '@/stores/auth';
import {useRouter} from 'vue-router';
import logo from '@/assets/images/logo.png';

const isCollapsed = ref(false);
const auth = useAuthStore();
const router = useRouter();
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/auth/login');
};
</script>

<style scoped>
.admin-sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 260px;
  background: linear-gradient(135deg, #0a1f1a 0%, #0d2820 50%, #0f2d24 100%);
  color: white;
  transition: width 0.3s ease;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.admin-sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  min-height: 80px;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
  min-width: 0;
}

.sidebar-logo {
  height: 40px;
  width: 40px;
  object-fit: contain;
  flex-shrink: 0;
}

.brand-text {
  font-size: 1.2rem;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #00ff88;
}

.collapse-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.collapse-btn:hover {
  background: rgba(0, 255, 136, 0.2);
  color: #00ff88;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
  overflow-x: hidden;
}

.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.875rem 1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 500;
  transition: all 0.3s ease;
  cursor: pointer;
  border-left: 3px solid transparent;
  position: relative;
}

.admin-sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 0.875rem 0;
}

.nav-item:hover {
  background: rgba(0, 255, 136, 0.1);
  color: #00ff88;
  border-left-color: #00ff88;
}

.nav-item.active,
.nav-item.router-link-active {
  background: rgba(0, 255, 136, 0.15);
  color: #00ff88;
  border-left-color: #00ff88;
}

.nav-icon {
  font-size: 1.4rem;
  flex-shrink: 0;
  width: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 1rem 0.5rem;
}

.logout-item {
  margin-top: auto;
  cursor: pointer;
}

.logout-item:hover {
  background: rgba(255, 68, 68, 0.1);
  color: #ff4444;
  border-left-color: #ff4444;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .admin-sidebar {
    width: 70px;
  }

  .admin-sidebar.collapsed {
    width: 70px;
  }

  .collapse-btn {
    display: none;
  }

  .brand-text,
  .nav-text {
    display: none;
  }

  .nav-item {
    justify-content: center;
    padding: 0.875rem 0;
  }
}
</style>