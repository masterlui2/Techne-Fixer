<!-- src/components/common/Navbar.vue -->
<template>
  <nav v-if="!isHiddenPage" class="app-navbar" :class="{ 'scrolled': isScrolled, 'auth-page': isAuthPage }">
    <div class="navbar-brand">
      <router-link to="/">
        <img :src="logo" alt="Techne-Fixer Logo" class="app-logo" />
        <text class="brand-name">Techne-Fixer</text>
      </router-link>
    </div>

    <!-- Desktop Navigation -->
    <div class="navbar-links desktop-nav">
      <router-link to="/" class="nav-button">Home</router-link>
      <router-link to="/services" class="nav-button">Services</router-link>
      <router-link to="/portfolio" class="nav-button">Portfolio</router-link>
      <router-link to="/about" class="nav-button">About</router-link>
      <a @click="scrollToContact" class="nav-button">Contact</a>

      <!-- Public/Guest Links -->
      <template v-if="!isAuthenticated">
        <router-link :to="{name: 'Login'}" class="btn btn-primary">Login</router-link>
        <router-link :to="{name: 'Register'}" class="btn btn-secondary">Register</router-link>
      </template>

      <template v-else-if="isAdmin">
        <router-link to="/admin/dashboard" class="btn btn-primary">Dashboard</router-link>
      </template>

      <!-- Customer Logout -->
      <template v-else>
        <a @click="handleLogout" class="btn btn-logout">Logout</a>
      </template>
    </div>

    <!-- Mobile Hamburger Button -->
    <button 
      class="hamburger-btn" 
      @click.stop="toggleMobileMenu"
      aria-label="Toggle menu">
      <span class="hamburger-line" :class="{ 'open': isMobileMenuOpen }"></span>
      <span class="hamburger-line" :class="{ 'open': isMobileMenuOpen }"></span>
      <span class="hamburger-line" :class="{ 'open': isMobileMenuOpen }"></span>
    </button>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" :class="{ 'active': isMobileMenuOpen }" @click="closeMobileMenu"></div>

    <!-- Mobile Side Menu -->
    <div class="mobile-menu" :class="{ 'active': isMobileMenuOpen }">
      <div class="mobile-menu-header">
        <img :src="logo" alt="Techne-Fixer Logo" class="mobile-logo" />
        <button class="close-btn" @click="closeMobileMenu" aria-label="Close menu">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="mobile-menu-links">
        <router-link to="/" class="mobile-nav-link" @click="closeMobileMenu">
          <span class="link-icon">🏠</span>
          Home
        </router-link>
        <router-link to="/services" class="mobile-nav-link" @click="closeMobileMenu">
          <span class="link-icon">🔧</span>
          Services
        </router-link>
        <router-link to="/portfolio" class="mobile-nav-link" @click="closeMobileMenu">
          <span class="link-icon">💼</span>
          Portfolio
        </router-link>
        <router-link to="/about" class="mobile-nav-link" @click="closeMobileMenu">
          <span class="link-icon">ℹ️</span>
          About
        </router-link>
        <a @click="scrollToContactMobile" class="mobile-nav-link mobile-nav-link-clickable">
          <span class="link-icon">📧</span>
          Contact
        </a>

        <!-- Mobile Auth Buttons -->
        <template v-if="!isAuthenticated">
          <div class="mobile-auth-buttons">
            <router-link :to="{name: 'Login'}" class="btn btn-primary btn-mobile" @click="closeMobileMenu">
              Login
            </router-link>
            <router-link :to="{name: 'Register'}" class="btn btn-secondary btn-mobile" @click="closeMobileMenu">
              Register
            </router-link>
          </div>
        </template>

        <!-- Mobile Customer Logout -->
        <template v-else-if="!isAdmin">
          <div class="mobile-auth-buttons">
            <a @click="handleLogout" class="btn btn-logout btn-mobile">
              🚪 Logout
            </a>
          </div>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRoute, useRouter } from 'vue-router';
import logo from '@/assets/images/logo.png';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const isAuthenticated = computed(() => !!auth.token);
const isAdmin = computed(() => auth.user?.role === 'admin');
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

// Check if current page is an auth page (login/register)
const isAuthPage = computed(() => {
  return route.path === '/auth/login' 
    || route.path === '/auth/register' 
    || route.path === '/auth/forgot-password'
});

const isHiddenPage = computed(() => {
  return route.meta?.hideNavbar === true;
});

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  
  // Prevent body scroll when menu is open
  if (isMobileMenuOpen.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
  document.body.style.overflow = '';
};

const handleLogout = async () => {
  closeMobileMenu();
  await auth.logout();
  router.push('/auth/login');
};

const scrollToContact = () => {
  const contactSection = document.querySelector('.contact-section, #contact, [id*="contact"]');
  
  if (contactSection) {
    const navbarHeight = 92;
    const elementPosition = contactSection.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - navbarHeight;

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    });
  } else {
    console.warn('Contact section not found on this page');
  }
};

const scrollToContactMobile = () => {
  closeMobileMenu();
  setTimeout(() => {
    scrollToContact();
  }, 300);
};

// Close mobile menu when route changes
watch(() => route.path, () => {
  closeMobileMenu();
});

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.app-navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: transparent;
  padding: 1rem 2rem;
  color: white;
  box-shadow: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1100;
  transition: all 0.3s ease;
}

/* Navbar when scrolled OR on auth pages */
.app-navbar.scrolled,
.app-navbar.auth-page {
  background-color: rgba(10, 31, 26, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
  z-index: 1;
  position: relative;
}

.navbar-brand a {
  display: flex;
  align-items: center;
  text-decoration: none;
  pointer-events: auto;
}

.brand-name {
  font-size: 1.5em;
  font-weight: bold;
  margin-left: 0.5rem;
  vertical-align: middle;
  color: white;
}

.app-logo {
  height: 60px;
  width: auto;
  vertical-align: middle;
}

.navbar-links {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.navbar-links .nav-button {
  padding: 0.5rem 1rem;
  border-radius: 5px;
  background-color: transparent;
  color: white;
  border: 1px solid transparent;
  transition: all 0.3s ease;
  text-decoration: none;
  font-weight: 500;
  white-space: nowrap;
  cursor: pointer;
}

.navbar-links .nav-button:hover {
  background-color: rgba(255, 255, 255, 0.15);
  color: #00ff88;
  border-color: #00ff88;
}

.navbar-links .nav-button.router-link-active {
  background-color: rgba(0, 255, 136, 0.2);
  color: #00ff88;
  border-color: #00ff88;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 5px;
  text-align: center;
  transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
  white-space: nowrap;
  text-decoration: none;
  display: inline-block;
  font-weight: 600;
  cursor: pointer;
}

.btn-primary {
  background-color: #00ff88;
  color: #0a1f1a;
  border: 1px solid #00ff88;
}

.btn-primary:hover {
  background-color: #00dd77;
  border-color: #00dd77;
}

.btn-secondary {
  background-color: transparent;
  color: white;
  border: 1px solid white;
}

.btn-secondary:hover {
  background-color: white;
  color: #0a1f1a;
}

.btn-logout {
  background-color: transparent;
  color: #ff4444;
  border: 1px solid #ff4444;
}

.btn-logout:hover {
  background-color: rgba(255, 68, 68, 0.15);
  border-color: #ff6666;
  color: #ff6666;
}

/* Hamburger Button - Hidden on Desktop */
.hamburger-btn {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  z-index: 1101;
  position: relative;
  flex-shrink: 0;
  pointer-events: auto;
  min-width: 40px;
  min-height: 40px;
}

.hamburger-line {
  width: 25px;
  height: 3px;
  background-color: white;
  transition: all 0.3s ease;
  border-radius: 2px;
  pointer-events: none;
}

.hamburger-line.open:nth-child(1) {
  transform: rotate(45deg) translate(7px, 7px);
}

.hamburger-line.open:nth-child(2) {
  opacity: 0;
}

.hamburger-line.open:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -7px);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.mobile-menu-overlay.active {
  opacity: 1;
  pointer-events: auto;
}

/* Mobile Side Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  right: -100%;
  width: 300px;
  max-width: 80%;
  height: 100vh;
  background: linear-gradient(135deg, #0a1f1a 0%, #0d2820 50%, #0f2d24 100%);
  z-index: 1100;
  transition: right 0.3s ease;
  overflow-y: auto;
  box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
}

.mobile-menu.active {
  right: 0;
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-logo {
  height: 45px;
  width: auto;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease;
}

.close-btn:hover {
  transform: rotate(90deg);
}

.mobile-menu-links {
  display: flex;
  flex-direction: column;
  padding: 1rem 0;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: white;
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 500;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  cursor: pointer;
}

.mobile-nav-link:hover,
.mobile-nav-link.router-link-active {
  background-color: rgba(0, 255, 136, 0.1);
  border-left-color: #00ff88;
  color: #00ff88;
}

.link-icon {
  font-size: 1.3rem;
}

.mobile-auth-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5rem;
  margin-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-mobile {
  width: 100%;
  text-align: center;
  padding: 0.75rem 1rem;
  font-size: 1rem;
}

/* Responsive - Mobile */
@media (max-width: 767px) {
  .desktop-nav {
    display: none;
  }

  .hamburger-btn {
    display: flex;
  }

  .mobile-menu-overlay {
    display: block;
  }

  .navbar-brand {
    flex: 1;
    min-width: 0;
  }

  .brand-name {
    font-size: 1.1em;
  }
  
  .app-logo {
    height: 45px;
  }

  .app-navbar {
    padding: 0.875rem 1rem;
    gap: 0.75rem;
  }

  .mobile-menu {
    width: 300px;
    max-width: 85%;
  }
}

/* Extra small mobile: 360px and below */
@media (max-width: 375px) {
  .brand-name {
    font-size: 1em;
  }

  .app-logo {
    height: 40px;
  }

  .app-navbar {
    padding: 0.75rem 0.875rem;
  }

  .mobile-menu {
    width: 280px;
  }
}

/* Tablet: 768px - 1023px */
@media (min-width: 768px) and (max-width: 1023px) {
  .mobile-menu,
  .mobile-menu-overlay,
  .hamburger-btn {
    display: none !important;
  }

  .app-navbar {
    padding: 1rem 1.5rem;
  }

  .brand-name {
    font-size: 1.3em;
  }

  .app-logo {
    height: 50px;
  }

  .navbar-links {
    gap: 1rem;
  }

  .navbar-links .nav-button {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }

  .btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }
}

/* Desktop: 1024px and up */
@media (min-width: 1024px) {
  .mobile-menu,
  .mobile-menu-overlay,
  .hamburger-btn {
    display: none !important;
  }
}
</style>