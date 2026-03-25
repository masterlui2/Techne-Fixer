<!-- src/views/public/InquiryView.vue -->
<template>
  <section class="inquiry-page">
    <div class="inquiry-container">

      <!-- Card -->
      <div class="inquiry-card">

        <!-- ── Header ── -->
        <div class="card-header">
          <div class="header-inner">
            <div class="brand">
              <div class="brand-logo">
                <img :src="logo" alt="Techne Fixer" class="logo-img" />
              </div>
              <div class="brand-text">
                <span class="brand-name">Techne Fixer</span>
                <span class="brand-sub">Tech Repair Services</span>
              </div>
            </div>
            <router-link to="/" class="back-link">← Home</router-link>
          </div>

          <div class="header-meta">
            <div class="trust-badges">
              <span class="badge"><span class="badge-check">✓</span> Fast response</span>
              <span class="badge"><span class="badge-check">✓</span> Professional</span>
              <span class="badge"><span class="badge-check">✓</span> Trusted</span>
            </div>
            <span class="form-label">Service Request</span>
          </div>
        </div>

        <!-- ── Body ── -->
        <div class="card-body">

          <!-- Success State -->
          <transition name="fade">
            <div v-if="submitSuccess" class="success-state">
              <div class="success-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                  <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="#00ff88" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M22 4L12 14.01l-3-3" stroke="#00ff88" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h3>Inquiry Submitted!</h3>
              <p>Thank you! We've received your request and will contact you as soon as possible.</p>
              <button class="btn-outline" @click="resetForm">Submit Another</button>
            </div>
          </transition>

          <form v-if="!submitSuccess" @submit.prevent="handleSubmit" novalidate>

            <!-- Error Banner -->
            <transition name="slide-down">
              <div v-if="submitError" class="error-banner">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                {{ submitError }}
              </div>
            </transition>

            <!-- ── Section 1: Contact Info ── -->
            <div class="form-section">
              <div class="section-header">
                <h3 class="section-title">
                  <span class="step-badge step-active">1</span>
                  Contact Information
                </h3>
                <p class="required-note">Fields marked <span class="req">*</span> are required</p>
              </div>

              <div class="fields-grid">
                <!-- Full Name -->
                <div class="field-group">
                  <label class="field-label">Full Name <span class="req">*</span></label>
                  <input
                    type="text"
                    v-model="form.name"
                    :class="['field-input', { 'field-error': errors.name }]"
                    placeholder="Juan Dela Cruz"
                  />
                  <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
                </div>

                <!-- Contact Number -->
                <div class="field-group">
                  <label class="field-label">Contact Number <span class="req">*</span></label>
                  <input
                    type="tel"
                    v-model="form.contact_number"
                    :class="['field-input', { 'field-error': errors.contact_number }]"
                    placeholder="09XX XXX XXXX"
                    inputmode="numeric"
                    maxlength="11"
                  />
                  <span class="field-hint">11 digits (e.g., 09XXXXXXXXX)</span>
                  <span v-if="errors.contact_number" class="error-msg">{{ errors.contact_number }}</span>
                </div>

                <!-- Email -->
                <div class="field-group">
                  <label class="field-label">Email Address <span class="req">*</span></label>
                  <input
                    type="email"
                    v-model="form.email"
                    :class="['field-input', { 'field-error': errors.email }]"
                    placeholder="you@example.com"
                  />
                  <span v-if="errors.email" class="error-msg">{{ errors.email }}</span>
                </div>

                <!-- Service Location -->
                <div class="field-group">
                  <label class="field-label">Service Location <span class="req">*</span></label>
                  <input
                    type="text"
                    v-model="form.service_location"
                    :class="['field-input', { 'field-error': errors.service_location }]"
                    placeholder="Street, Barangay, City"
                  />
                  <span v-if="errors.service_location" class="error-msg">{{ errors.service_location }}</span>
                </div>
              </div>
            </div>

            <!-- ── Section 2: Service Details ── -->
            <div class="form-section">
              <div class="section-header">
                <h3 class="section-title">
                  <span class="step-badge step-active">2</span>
                  Service Details
                </h3>
              </div>

              <div class="fields-grid fields-grid--3">
                <!-- Category -->
                <div class="field-group">
                  <label class="field-label">Category <span class="req">*</span></label>
                  <div class="select-wrapper">
                    <select
                      v-model="form.category"
                      :class="['field-input', 'field-select', { 'field-error': errors.category }]"
                      :disabled="loadingServices"
                    >
                      <option value="">
                        {{ loadingServices ? 'Loading...' : 'Select a service' }}
                      </option>
                      <option
                        v-for="service in services"
                        :key="service.id"
                        :value="service.name"
                      >
                        {{ service.name }}
                      </option>
                    </select>
                    <span class="select-arrow">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </div>
                  <span v-if="errors.category" class="error-msg">{{ errors.category }}</span>
                </div>

                <!-- Urgency -->
                <div class="field-group">
                  <label class="field-label">How quick do you need a response? <span class="req">*</span></label>
                  <div class="select-wrapper">
                    <select
                      v-model="form.urgency"
                      :class="['field-input', 'field-select', { 'field-error': errors.urgency }]"
                    >
                      <option value="Normal">Normal (1–3 days)</option>
                      <option value="Urgent">Urgent (same/next day)</option>
                      <option value="Flexible">Flexible</option>
                    </select>
                    <span class="select-arrow">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </div>
                  <span v-if="errors.urgency" class="error-msg">{{ errors.urgency }}</span>
                </div>

                <!-- Device / Brand -->
                <div class="field-group">
                  <label class="field-label">
                    Device / Brand
                    <span class="field-optional">(optional)</span>
                  </label>
                  <input
                    type="text"
                    v-model="form.device_details"
                    class="field-input"
                    placeholder="e.g., HP Laptop 15s"
                  />
                </div>
              </div>

              <!-- Issue Description -->
              <div class="field-group mt-4">
                <label class="field-label">Issue Description <span class="req">*</span></label>
                <textarea
                  v-model="form.issue_description"
                  :class="['field-input', 'field-textarea', { 'field-error': errors.issue_description }]"
                  rows="4"
                  placeholder="Please describe the issue (symptoms, error messages, when it started, etc.)."
                ></textarea>
                <span v-if="errors.issue_description" class="error-msg">{{ errors.issue_description }}</span>
              </div>
            </div>

            <!-- ── Section 3: Additional Info ── -->
            <div class="form-section">
              <div class="section-header">
                <h3 class="section-title">
                  <span class="step-badge">3</span>
                  Additional Information
                  <span class="section-optional">(optional)</span>
                </h3>
              </div>

              <div class="fields-grid fields-grid--3">
                <!-- Referral Source -->
                <div class="field-group">
                  <label class="field-label">How did you learn about us?</label>
                  <div class="select-wrapper">
                    <select v-model="form.referral_source" class="field-input field-select">
                      <option value="">Select an option</option>
                      <option value="Facebook">Facebook</option>
                      <option value="Google Search">Google Search</option>
                      <option value="Referral">Referral (Friend/Family)</option>
                      <option value="Walk-in">Walk-in</option>
                      <option value="Other">Other</option>
                    </select>
                    <span class="select-arrow">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                  </div>
                </div>

                <!-- Preferred Schedule -->
                <div class="field-group">
                  <label class="field-label">Preferred Date and Time</label>
                  <input
                    type="datetime-local"
                    v-model="form.preferred_schedule"
                    class="field-input"
                  />
                </div>

                <!-- Photo Upload -->
                <div class="field-group">
                  <label class="field-label">Upload Photo</label>
                  <div
                    class="file-drop"
                    :class="{ 'file-drop--active': isDragging, 'file-drop--filled': photoPreview }"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop"
                    @click="$refs.photoInput.click()"
                  >
                    <img v-if="photoPreview" :src="photoPreview" alt="Preview" class="photo-preview" />
                    <div v-else class="file-drop-inner">
                      <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                        <path d="M21 15l-5-5L5 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <span>Click or drag photo here</span>
                      <span class="file-hint">Helps us assess the issue faster</span>
                    </div>
                    <button
                      v-if="photoPreview"
                      type="button"
                      class="photo-remove"
                      @click.stop="removePhoto"
                    >✕</button>
                  </div>
                  <input
                    ref="photoInput"
                    type="file"
                    accept="image/*"
                    class="hidden-input"
                    @change="handleFileChange"
                  />
                  <span v-if="errors.photo" class="error-msg">{{ errors.photo }}</span>
                </div>
              </div>
            </div>

            <!-- ── Sticky Submit ── -->
            <div class="form-footer">
              <button type="submit" class="btn-submit" :disabled="isSubmitting">
                <span v-if="!isSubmitting">Submit Inquiry</span>
                <span v-else class="btn-loading">
                  <svg class="spinner" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                  </svg>
                  Submitting...
                </span>
              </button>
              <p class="footer-note">We will review your request and contact you as soon as possible.</p>
            </div>

          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import logo from '@/assets/images/logo.png';

// ── API ──────────────────────────────────────────────────────────────────────
const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// ── Services (for category dropdown) ─────────────────────────────────────────
const services = ref([]);
const loadingServices = ref(true);

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

// ── Form State ────────────────────────────────────────────────────────────────
const form = ref({
  name: '',
  contact_number: '',
  email: '',
  service_location: '',
  category: '',
  urgency: 'Normal',
  device_details: '',
  issue_description: '',
  referral_source: '',
  preferred_schedule: '',
});

const errors        = ref({});
const isSubmitting  = ref(false);
const submitSuccess = ref(false);
const submitError   = ref('');

// ── Photo Upload ──────────────────────────────────────────────────────────────
const photoFile    = ref(null);
const photoPreview = ref('');
const isDragging   = ref(false);
const photoInput   = ref(null);

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) applyPhoto(file);
};

const handleDrop = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) applyPhoto(file);
};

const applyPhoto = (file) => {
  if (file.size > 5 * 1024 * 1024) {
    errors.value.photo = 'Photo must be under 5MB.';
    return;
  }
  photoFile.value = file;
  errors.value.photo = '';
  const reader = new FileReader();
  reader.onload = (e) => { photoPreview.value = e.target.result; };
  reader.readAsDataURL(file);
};

const removePhoto = () => {
  photoFile.value    = null;
  photoPreview.value = '';
  if (photoInput.value) photoInput.value.value = '';
};

// ── Validation ────────────────────────────────────────────────────────────────
const validate = () => {
  const e = {};

  if (!form.value.name.trim())
    e.name = 'Full name is required.';

  if (!form.value.contact_number.trim())
    e.contact_number = 'Contact number is required.';
  else if (!/^[0-9]{11}$/.test(form.value.contact_number))
    e.contact_number = 'Must be exactly 11 digits (e.g., 09XXXXXXXXX).';

  if (!form.value.email.trim())
    e.email = 'Email address is required.';
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email))
    e.email = 'Please enter a valid email address.';

  if (!form.value.service_location.trim())
    e.service_location = 'Service location is required.';

  if (!form.value.category)
    e.category = 'Please select a category.';

  if (!form.value.urgency)
    e.urgency = 'Please select urgency.';

  if (!form.value.issue_description.trim())
    e.issue_description = 'Issue description is required.';
  else if (form.value.issue_description.trim().length < 10)
    e.issue_description = 'Please provide at least 10 characters.';

  errors.value = e;
  return Object.keys(e).length === 0;
};

// ── Submit ────────────────────────────────────────────────────────────────────
const handleSubmit = async () => {
  submitError.value = '';
  if (!validate()) {
    // Scroll to first error
    const firstError = document.querySelector('.field-error');
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  isSubmitting.value = true;

  try {
    const payload = new FormData();
    Object.entries(form.value).forEach(([key, val]) => {
      if (val !== '' && val !== null) payload.append(key, val);
    });
    if (photoFile.value) payload.append('photo', photoFile.value);

    const res = await fetch(`${API_URL}/inquiries`, {
      method: 'POST',
      body: payload,
    });

    if (!res.ok) {
      const data = await res.json();
      if (res.status === 422 && data?.errors) {
        const mapped = {};
        for (const [field, messages] of Object.entries(data.errors)) {
          mapped[field] = Array.isArray(messages) ? messages[0] : messages;
        }
        errors.value = mapped;
        return;
      }
      throw new Error(data?.message || `HTTP ${res.status}`);
    }

    submitSuccess.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });

  } catch (err) {
    submitError.value = err.message || 'Something went wrong. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

// ── Reset ─────────────────────────────────────────────────────────────────────
const resetForm = () => {
  form.value = {
    name: '', contact_number: '', email: '', service_location: '',
    category: '', urgency: 'Normal', device_details: '', issue_description: '',
    referral_source: '', preferred_schedule: '',
  };
  errors.value       = {};
  submitSuccess.value = false;
  submitError.value  = '';
  removePhoto();
};

onMounted(fetchServices);
</script>

<style scoped>
/* ── Page ── */
.inquiry-page {
  min-height: 100vh;
  background: #f1f5f9;
  padding: 2rem 0 4rem;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
}

.inquiry-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* ── Card ── */
.inquiry-card {
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

/* ── Header ── */
.card-header {
  position: sticky;
  top: 0;
  z-index: 10;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 1.75rem;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.875rem;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-logo {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.logo-img {
  width: 36px;
  height: 36px;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.brand-name {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
}

.brand-sub {
  font-size: 0.72rem;
  color: #64748b;
}

.back-link {
  font-size: 0.8rem;
  color: #64748b;
  text-decoration: none;
  transition: color 0.2s;
}

.back-link:hover { color: #3b82f6; }

.header-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.trust-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.72rem;
  color: #475569;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
}

.badge-check { color: #00cc6a; font-weight: 700; }

.form-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: #3b82f6;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

/* ── Body ── */
.card-body {
  padding: 1.75rem;
}

/* ── Success ── */
.success-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 3rem 1rem;
  gap: 1rem;
}

.success-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #f0fdf4;
  border: 2px solid #bbf7d0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.success-state h3 {
  font-size: 1.6rem;
  font-weight: 800;
  color: #0f172a;
}

.success-state p {
  color: #64748b;
  font-size: 0.95rem;
  max-width: 340px;
  line-height: 1.6;
}

/* ── Error Banner ── */
.error-banner {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fff0f0;
  border: 1px solid #fecaca;
  color: #dc2626;
  border-radius: 10px;
  padding: 0.875rem 1rem;
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

/* ── Form Sections ── */
.form-section {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.25rem;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.25rem;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.section-optional {
  font-size: 0.78rem;
  font-weight: 400;
  color: #94a3b8;
}

.step-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  font-size: 0.72rem;
  font-weight: 700;
  background: #e2e8f0;
  color: #475569;
  flex-shrink: 0;
}

.step-badge.step-active {
  background: #dbeafe;
  color: #1d4ed8;
}

.required-note {
  font-size: 0.75rem;
  color: #94a3b8;
  margin: 0;
}

/* ── Grid ── */
.fields-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.fields-grid--3 {
  grid-template-columns: repeat(3, 1fr);
}

.mt-4 { margin-top: 1rem; }

/* ── Field ── */
.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.field-label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #334155;
}

.field-optional {
  font-weight: 400;
  color: #94a3b8;
  font-size: 0.78rem;
  margin-left: 0.25rem;
}

.req { color: #ef4444; }

.field-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 9px;
  font-size: 0.875rem;
  color: #0f172a;
  background: #ffffff;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}

.field-input::placeholder { color: #94a3b8; }

.field-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}

.field-input.field-error {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.field-textarea {
  resize: vertical;
  min-height: 110px;
}

.field-hint {
  font-size: 0.72rem;
  color: #94a3b8;
}

.error-msg {
  font-size: 0.75rem;
  color: #ef4444;
}

/* ── Select ── */
.select-wrapper {
  position: relative;
}

.field-select {
  appearance: none;
  padding-right: 2.5rem;
  cursor: pointer;
}

.select-arrow {
  pointer-events: none;
  position: absolute;
  right: 0.875rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  display: flex;
  align-items: center;
}

/* ── File Drop ── */
.file-drop {
  position: relative;
  border: 1.5px dashed #cbd5e1;
  border-radius: 10px;
  background: #ffffff;
  cursor: pointer;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.2s, background 0.2s;
  overflow: hidden;
}

.file-drop:hover,
.file-drop--active {
  border-color: #3b82f6;
  background: #eff6ff;
}

.file-drop--filled { border-style: solid; border-color: #86efac; }

.file-drop-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  padding: 1rem;
  color: #94a3b8;
  text-align: center;
}

.file-drop-inner span { font-size: 0.8rem; }
.file-hint { font-size: 0.7rem !important; color: #b0bec5; }

.photo-preview {
  width: 100%;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.photo-remove {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(0,0,0,0.55);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  font-size: 0.65rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.photo-remove:hover { background: rgba(0,0,0,0.8); }

.hidden-input {
  display: none;
}

/* ── Footer / Submit ── */
.form-footer {
  position: sticky;
  bottom: 0;
  margin: 0 -1.75rem -1.75rem;
  padding: 1rem 1.75rem;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(10px);
  border-top: 1px solid #e2e8f0;
}

.btn-submit {
  width: 100%;
  padding: 0.875rem;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-family: inherit;
}

.btn-submit:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-outline {
  margin-top: 0.5rem;
  background: transparent;
  border: 1.5px solid #2563eb;
  color: #2563eb;
  padding: 0.6rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}

.btn-outline:hover {
  background: #eff6ff;
}

.footer-note {
  margin: 0.5rem 0 0;
  text-align: center;
  font-size: 0.75rem;
  color: #94a3b8;
}

/* ── Spinner ── */
.spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ── */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from { opacity: 0; transform: translateY(-8px); }

/* ── Responsive ── */
@media (max-width: 768px) {
  .card-body { padding: 1.25rem; }
  .form-footer { margin: 0 -1.25rem -1.25rem; padding: 1rem 1.25rem; }
  .form-section { padding: 1rem 1.25rem; }
  .fields-grid { grid-template-columns: 1fr; }
  .fields-grid--3 { grid-template-columns: 1fr; }
  .header-meta { flex-direction: column; align-items: flex-start; }
}

@media (max-width: 480px) {
  .card-header { padding: 1rem; }
  .card-body { padding: 1rem; }
  .form-footer { margin: 0 -1rem -1rem; padding: 0.875rem 1rem; }
  .trust-badges { display: none; }
}
</style>