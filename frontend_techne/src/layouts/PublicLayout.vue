<!-- src/layouts/PublicLayout.vue -->
<template>
  <div class="public-layout">
    <Navbar />

    <main class="public-content">
      <slot></slot>
    </main>

    <footer class="public-footer">
      <div class="footer-container">
        <!-- Footer Top Section -->
        <div class="footer-top">
          <div class="footer-brand">
            <img :src="logo" alt="Techne-Fixer Logo" class="footer-logo" />
            <h3>Techne-Fixer</h3>
            <p>Professional repair services for electronics and appliances. Quality workmanship, reliable service.</p>
          </div>

          <div class="footer-links">
            <div class="footer-column">
              <h4>Quick Links</h4>
              <router-link to="/">Home</router-link>
              <router-link to="/services">Services</router-link>
              <router-link to="/portfolio">Portfolio</router-link>
              <router-link to="/about">About Us</router-link>
              <a @click="scrollToContact" class="nav-button">Contact</a>
            </div>

            <div class="footer-column">
              <h4>Our Services</h4>
              <router-link to="/services">All Services</router-link>
              <router-link to="/services#laptop">Laptop Repair</router-link>
              <router-link to="/services#printer">Printer Repair</router-link>
              <router-link to="/services#washing">Washing Machine</router-link>
              <router-link to="/services#cctv">CCTV Installation</router-link>
            </div>

            <div class="footer-column">
              <h4>Support</h4>
              <router-link to="/about">About Us</router-link>
              <router-link to="/contact">Get a Quote</router-link>
              <router-link to="/portfolio">Our Work</router-link>
              <a href="#">FAQ</a>
              <a href="#">Warranty Info</a>
            </div>
          </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="footer-bottom">
          <p>&copy; 2026 Techne-Fixer. All rights reserved.</p>
          <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <span class="divider">|</span>
            <a href="#">Terms of Service</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navbar from '@/components/common/NavBar.vue';
import logo from '@/assets/images/logo.png';

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

</script>

<style scoped>
.public-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.public-content {
  flex-grow: 1;
}

/* Footer Styles */
.public-footer {
  background: linear-gradient(135deg, #0a1f1a 0%, #0d2820 50%, #0f2d24 100%);
  color: white;
  margin-top: 4rem;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 2rem 1.5rem;
}

.footer-top {
  display: grid;
  grid-template-columns: 1.5fr 2fr;
  gap: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-brand {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.footer-logo {
  height: 60px;
  width: 60px;
  object-fit: contain;
}

.footer-brand h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #00ff88;
  margin: 0;
}

.footer-brand p {
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.6;
  font-size: 0.95rem;
}

.footer-links {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.footer-column h4 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #00ff88;
  margin-bottom: 1rem;
}

.footer-column a {
  display: block;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  line-height: 1.6;
}

.footer-column a:hover {
  color: #00ff88;
  padding-left: 0.5rem;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1.5rem;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.6);
}

.footer-bottom p {
  margin: 0;
}

.footer-bottom-links {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.footer-bottom-links a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
  color: #00ff88;
}

.divider {
  color: rgba(255, 255, 255, 0.3);
}

/* Responsive Styles */
@media (max-width: 1024px) {
  .footer-top {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .footer-links {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .footer-container {
    padding: 2rem 1.5rem 1rem;
  }

  .footer-top {
    gap: 2rem;
  }

  .footer-links {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .footer-bottom-links {
    flex-wrap: wrap;
    justify-content: center;
  }

  .footer-logo {
    height: 50px;
    width: 50px;
  }

  .footer-brand h3 {
    font-size: 1.3rem;
  }
}

@media (max-width: 480px) {
  .footer-container {
    padding: 1.5rem 1rem 1rem;
  }

  .footer-brand p {
    font-size: 0.9rem;
  }

  .footer-column h4 {
    font-size: 1rem;
  }

  .footer-column a {
    font-size: 0.9rem;
  }
}
</style>