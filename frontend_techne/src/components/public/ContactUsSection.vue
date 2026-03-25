<!-- src/components/public/ContactUsSection.vue -->
<template>
  <section class="contact-section">
    <div class="contact-container">
      <div class="contact-content">
        <div class="contact-info">
          <div class="tag-badge">Get in Touch</div>
          <h2>Let's Build Something Amazing Together</h2>
          <p class="description">
            Have a project in mind? We'd love to hear from you. 
            Send us a message and we'll respond as soon as possible.
          </p>

          <div class="info-items">
            <div class="info-item">
              <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="info-details">
                <h4>Location</h4>
                <p>Davao City, Philippines</p>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M3 8L10.89 13.26C11.2187 13.4793 11.6049 13.5963 12 13.5963C12.3951 13.5963 12.7813 13.4793 13.11 13.26L21 8M5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7C21 6.46957 20.7893 5.96086 20.4142 5.58579C20.0391 5.21071 19.5304 5 19 5H5C4.46957 5 3.96086 5.21071 3.58579 5.58579C3.21071 5.96086 3 6.46957 3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="info-details">
                <h4>Email</h4>
                <p>info@techne-fixer.com</p>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.5953 1.99522 8.06579 2.16708 8.43376 2.48353C8.80173 2.79999 9.04207 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.8939 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5864 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="info-details">
                <h4>Phone</h4>
                <p>+63 123 456 7890</p>
              </div>
            </div>
          </div>

          <div class="social-links">
            <a href="#" class="social-link" aria-label="Facebook">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="contact-form-wrapper">
          <!-- Success Message -->
          <div v-show="submitSuccess" class="success-message">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="#00ff88" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 4L12 14.01l-3-3" stroke="#00ff88" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3>Message Sent!</h3>
            <p>Thank you for reaching out. We'll get back to you as soon as possible.</p>
            <button class="reset-btn" @click="resetForm">Send Another Message</button>
          </div>

          <form v-show="!submitSuccess" @submit.prevent="handleSubmit" class="contact-form">
            <!-- Error Banner -->
            <div v-if="submitError" class="error-banner">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              {{ submitError }}
            </div>

            <div class="form-group">
              <label for="name">Full Name <span class="required">*</span></label>
              <input 
                type="text" 
                id="name" 
                v-model="formData.name"
                :class="{ 'input-error': errors.name }"
                placeholder="John Doe"
              />
              <span v-if="errors.name" class="field-error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="email">Email Address <span class="required">*</span></label>
              <input 
                type="email" 
                id="email" 
                v-model="formData.email"
                :class="{ 'input-error': errors.email }"
                placeholder="john@example.com"
              />
              <span v-if="errors.email" class="field-error">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input 
                type="tel" 
                id="phone" 
                v-model="formData.phone"
                :class="{ 'input-error': errors.phone }"
                placeholder="+63 123 456 7890"
              />
              <span v-if="errors.phone" class="field-error">{{ errors.phone }}</span>
            </div>

            <div class="form-group full-width">
              <label for="message">Message <span class="required">*</span></label>
              <textarea 
                id="message" 
                v-model="formData.message"
                :class="{ 'input-error': errors.message }"
                rows="5"
                placeholder="Tell us about your project..."
              ></textarea>
              <span v-if="errors.message" class="field-error">{{ errors.message }}</span>
            </div>

            <!-- reCAPTCHA v2 -->
            <div class="form-group full-width recaptcha-wrapper">
              <div ref="recaptchaContainer" id="recaptcha-container"></div>
              <span v-if="errors.recaptcha" class="field-error">{{ errors.recaptcha }}</span>
            </div>

            <button type="submit" class="submit-btn" :disabled="isSubmitting">
              <span v-if="!isSubmitting">Send Message</span>
              <span v-else class="btn-loading">
                <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                </svg>
                Sending...
              </span>
              <svg v-if="!isSubmitting" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M18 2L9 11M18 2L12 18L9 11M18 2L2 8L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from '@/api/axios';

const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY || 'YOUR_RECAPTCHA_SITE_KEY';

const recaptchaContainer = ref(null);
const recaptchaWidgetId  = ref(null);
const recaptchaToken     = ref('');

const formData = ref({
  name: '',
  email: '',
  phone: '',
  message: '',
});

const errors       = ref({});
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError  = ref('');


// add these refs alongside the existing ones
const services = ref([]);
const loadingServices = ref(true);

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const fetchServices = async () => {
  try {
    const res = await fetch(`${API_URL}/services`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    services.value = data.data;
  } catch (err) {
    console.error('Failed to load services:', err);
  } finally {
    loadingServices.value = false;
  }
};


// ── reCAPTCHA ────────────────────────────────────────────────────────────────

const loadRecaptchaScript = () => {
  return new Promise((resolve) => {
    if (window.grecaptcha) { resolve(); return; }
    const script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
    script.async = true;
    script.defer = true;
    script.onload = resolve;
    document.head.appendChild(script);
  });
};

const renderRecaptcha = () => {
  if (!window.grecaptcha || !recaptchaContainer.value) return;
  recaptchaWidgetId.value = window.grecaptcha.render(recaptchaContainer.value, {
    sitekey: RECAPTCHA_SITE_KEY,
    callback: (token) => { recaptchaToken.value = token; errors.value.recaptcha = ''; },
    'expired-callback': () => { recaptchaToken.value = ''; },
    'error-callback':   () => { recaptchaToken.value = ''; },
  });
};

onMounted(async () => {
  fetchServices();
  await loadRecaptchaScript();
  // grecaptcha.ready ensures the widget API is available
  window.grecaptcha.ready(renderRecaptcha);
});

onUnmounted(() => {
  // Clean up widget to avoid duplicate renders on hot-reload
  if (recaptchaWidgetId.value !== null && window.grecaptcha) {
    try { window.grecaptcha.reset(recaptchaWidgetId.value); } catch (_) {}
  }
});

// ── Validation ───────────────────────────────────────────────────────────────

const validate = () => {
  const e = {};

  if (!formData.value.name.trim())
    e.name = 'Full name is required.';

  if (!formData.value.email.trim())
    e.email = 'Email address is required.';
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email))
    e.email = 'Please enter a valid email address.';

  if (formData.value.phone && !/^[\d\s\+\-\(\)]{7,20}$/.test(formData.value.phone))
    e.phone = 'Please enter a valid phone number.';

  if (!formData.value.message.trim())
    e.message = 'Message is required.';
  else if (formData.value.message.trim().length < 10)
    e.message = 'Message must be at least 10 characters.';

  if (!recaptchaToken.value)
    e.recaptcha = 'Please complete the reCAPTCHA verification.';

  errors.value = e;
  return Object.keys(e).length === 0;
};

// ── Submit ───────────────────────────────────────────────────────────────────

const handleSubmit = async () => {
  submitError.value = '';
  if (!validate()) return;

  isSubmitting.value = true;

  try {
    await axios.post('/contact', {
      name:            formData.value.name,
      email:           formData.value.email,
      phone:           formData.value.phone,
      message:         formData.value.message,
      recaptcha_token: recaptchaToken.value,
    });

    submitSuccess.value = true;

  } catch (err) {
    const data = err.response?.data;
    const status = err.response?.status;

    if (status === 422 && data?.errors) {
      // Map Laravel validation errors to fields
      const laravelErrors = {};
      for (const [field, messages] of Object.entries(data.errors)) {
        laravelErrors[field] = Array.isArray(messages) ? messages[0] : messages;
      }
      errors.value = laravelErrors;
    } else {
      submitError.value = data?.message || 'Something went wrong. Please try again.';
    }
  } finally {
    isSubmitting.value = false;
    // Always reset reCAPTCHA after a submission attempt
    if (recaptchaWidgetId.value !== null && window.grecaptcha) {
      window.grecaptcha.reset(recaptchaWidgetId.value);
      recaptchaToken.value = '';
    }
  }
};

const resetForm = () => {
  formData.value     = { name: '', email: '', phone: '', message: '' };
  errors.value       = {};
  submitSuccess.value = false;
  submitError.value  = '';
  recaptchaToken.value = '';
  if (recaptchaWidgetId.value !== null && window.grecaptcha) {
    window.grecaptcha.reset(recaptchaWidgetId.value);
  }
};
</script>

<style scoped>
.contact-section {
  background: #f8f9fa;
  padding: 5rem 0;
  position: relative;
  width: 100%;
}

.contact-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
}

.contact-info {
  position: sticky;
  top: 120px;
}

.tag-badge {
  display: inline-block;
  background: #e6f7f0;
  border: 2px solid #00ff88;
  color: #0a1f1a;
  padding: 0.5rem 1.5rem;
  border-radius: 24px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.contact-info h2 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1.2;
  margin-bottom: 1rem;
}

.description {
  font-size: 1.1rem;
  color: #666;
  line-height: 1.7;
  margin-bottom: 2.5rem;
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.info-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.info-icon {
  width: 48px;
  height: 48px;
  background: #f0f0f0;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #00ff88;
  flex-shrink: 0;
}

.info-details h4 {
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.25rem;
}

.info-details p {
  font-size: 0.95rem;
  color: #666;
}

.social-links {
  display: flex;
  gap: 1rem;
}

.social-link {
  width: 44px;
  height: 44px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-link:hover {
  background: #00ff88;
  color: #0a1f1a;
  transform: translateY(-4px);
}

/* ── Form Wrapper ── */
.contact-form-wrapper {
  background: #ffffff;
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}

/* ── Success State ── */
.success-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 3rem 2rem;
  gap: 1rem;
}

.success-message h3 {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1a1a1a;
}

.success-message p {
  color: #666;
  font-size: 1rem;
  line-height: 1.6;
  max-width: 320px;
}

.reset-btn {
  margin-top: 0.5rem;
  background: transparent;
  border: 2px solid #00ff88;
  color: #0a1f1a;
  padding: 0.75rem 1.75rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.reset-btn:hover {
  background: #00ff88;
}

/* ── Error Banner ── */
.error-banner {
  grid-column: 1 / -1;
  background: #fff0f0;
  border: 1px solid #ffcccc;
  color: #cc0000;
  border-radius: 8px;
  padding: 0.875rem 1rem;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* ── Form ── */
.contact-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1a1a1a;
}

.required {
  color: #e53e3e;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.875rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  font-family: inherit;
  background: white;
  color: #1a1a1a;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #00ff88;
  box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
}

.form-group input.input-error,
.form-group select.input-error,
.form-group textarea.input-error {
  border-color: #e53e3e;
  box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.field-error {
  font-size: 0.8rem;
  color: #e53e3e;
  margin-top: 0.1rem;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

/* ── reCAPTCHA ── */
.recaptcha-wrapper {
  align-items: flex-start;
}

/* ── Submit Button ── */
.submit-btn {
  grid-column: 1 / -1;
  background: #00ff88;
  color: #0a1f1a;
  border: none;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 255, 136, 0.3);
}

.submit-btn:hover:not(:disabled) {
  background: #00dd77;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 255, 136, 0.4);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .contact-content {
    grid-template-columns: 1fr;
    gap: 3rem;
  }

  .contact-info {
    position: static;
  }

  .contact-info h2 {
    font-size: 2rem;
  }
}

@media (max-width: 768px) {
  .contact-section {
    padding: 3rem 0;
  }

  .contact-info h2 {
    font-size: 1.75rem;
  }

  .description {
    font-size: 1rem;
  }

  .contact-form-wrapper {
    padding: 1.5rem;
  }

  .contact-form {
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }

  .info-items {
    gap: 1.25rem;
  }

  .social-links {
    gap: 0.75rem;
  }

  .social-link {
    width: 40px;
    height: 40px;
  }
}
</style>