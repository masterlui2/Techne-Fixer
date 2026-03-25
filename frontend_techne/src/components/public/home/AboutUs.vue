<template>
  <div class="about-page">
    <!-- Location Section -->
    <section id="location" class="location-section">
      <div class="container-custom">
        <div class="section-header">
          <h2 class="section-title">Visit Our Service Center</h2>
          <p class="section-description">
            Drop by our location for consultations, walk-in diagnostics, or to
            pick up completed repairs.
          </p>
        </div>

        <div class="location-grid">
          <!-- Map Container -->
          <div class="map-wrapper">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4078.4335142038844!2d125.49317867510116!3d7.027566117100341!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f90d345435ca29%3A0x956b6d7472a90432!2sTechne%20Fixer%20Computer%20and%20Laptop%20Repair%20Services%2F%20CCTV%20INSTALLATION!5e1!3m2!1sen!2sph!4v1764341286198!5m2!1sen!2sph"
              class="map-iframe"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Techne-Fixer Location"
            ></iframe>
            <a
              href="https://maps.google.com/?q=Techne+Fixer+Computer+and+Laptop+Repair+Services+CCTV+INSTALLATION"
              target="_blank"
              class="map-button"
            >
              <svg
                class="icon-small"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                />
              </svg>
              View larger map
            </a>
          </div>

          <!-- Business Hours Card -->
          <div class="hours-card">
            <h3 class="hours-title">Business Hours</h3>
            <p class="hours-subtitle">We're here when you need us.</p>

            <div class="hours-schedule">
              <div class="hours-row">
                <span class="hours-day">Monday - Saturday</span>
                <span class="hours-time">9:00 AM – 6:00 PM</span>
              </div>
            </div>

            <div class="info-note">
              <svg class="icon-small" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Tap the map for directions</span>
            </div>

            <div class="divider"></div>

            <div class="contact-info">
              <div class="contact-item">
                <div class="contact-icon">
                  <svg
                    class="icon-small"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                    />
                  </svg>
                </div>
                <span>Call us for appointments</span>
              </div>
              <div class="contact-item">
                <div class="contact-icon">
                  <svg
                    class="icon-small"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                  </svg>
                </div>
                <span>Email for inquiries</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "AboutUs",
};
</script>

<style scoped>
.about-page {
  min-height: 100vh;
  background: #ffffff;
}

.container-custom {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Location Section */
.location-section {
  padding: 5rem 0;
  background: #f8f9fa;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 1rem 0;
}

.section-description {
  font-size: 1.125rem;
  color: #666;
  max-width: 42rem;
  margin: 0 auto;
}

.location-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 1024px) {
  .location-grid {
    grid-template-columns: 2fr 1fr;
  }
}

.map-wrapper {
  position: relative;
  height: 400px;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (min-width: 1024px) {
  .map-wrapper {
    height: 500px;
  }
}

.map-iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

.map-button {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: #ffffff;
  color: #1a1a1a;
  padding: 0.75rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  text-decoration: none;
}

.map-button:hover {
  background: #00ff88;
  color: #0a1f1a;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 255, 136, 0.3);
}

.icon-small {
  width: 1.25rem;
  height: 1.25rem;
}

/* Hours Card */
.hours-card {
  background: #ffffff;
  border: 2px solid #e0e0e0;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.hours-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
}

.hours-subtitle {
  color: #666;
  margin: 0 0 1.5rem 0;
}

.hours-schedule {
  margin-bottom: 1.5rem;
}

.hours-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
}

.hours-day {
  color: #1a1a1a;
  font-weight: 600;
}

.hours-time {
  color: #00ff88;
  font-weight: 700;
}

.info-note {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #666;
  background: #e8f5e9;
  border: 1px solid #00ff88;
  border-radius: 0.5rem;
  padding: 0.75rem;
  margin-bottom: 1.5rem;
}

.divider {
  height: 1px;
  background: #e0e0e0;
  margin: 1.5rem 0;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #444;
}

.contact-icon {
  flex-shrink: 0;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e8f5e9;
  color: #00ff88;
}

/* Responsive */
@media (max-width: 1024px) {
  .container-custom {
    padding: 0 1.25rem;
  }
}

@media (max-width: 768px) {
  .location-section {
    padding: 3rem 0;
  }

  .container-custom {
    padding: 0 1rem;
  }

  .section-header {
    margin-bottom: 2rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .section-description {
    font-size: 1rem;
  }

  .location-grid {
    gap: 1.5rem;
  }

  .map-wrapper {
    height: 350px;
  }

  .hours-card {
    padding: 1.5rem;
  }
}

@media (max-width: 480px) {
  .location-section {
    padding: 2.5rem 0;
  }

  .container-custom {
    padding: 0 0.75rem;
  }

  .section-title {
    font-size: 1.75rem;
  }

  .section-description {
    font-size: 0.95rem;
  }

  .map-wrapper {
    height: 300px;
    border-radius: 0.75rem;
  }

  .map-button {
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
  }

  .hours-card {
    padding: 1.25rem;
    border-radius: 0.75rem;
  }

  .hours-title {
    font-size: 1.25rem;
  }

  .hours-subtitle {
    font-size: 0.9rem;
  }

  .hours-row {
    padding: 0.875rem;
    font-size: 0.9rem;
  }

  .contact-icon {
    width: 2.25rem;
    height: 2.25rem;
  }

  .contact-item span {
    font-size: 0.9rem;
  }

  .info-note {
    font-size: 0.8rem;
    padding: 0.625rem;
  }
}
</style>