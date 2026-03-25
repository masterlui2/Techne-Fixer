<template>
  <div class="portfolio-form-page">

    <div class="page-header">
      <router-link to="/admin/portfolio" class="back-link">← Back to Portfolio</router-link>
      <h1>Edit Project</h1>
      <p class="subtitle">Update the details for this project</p>
    </div>

    <div v-if="isLoading" class="loading-state">
      <div class="spinner-lg"></div>
      <p>Loading project...</p>
    </div>

    <div v-else-if="loadError" class="error-state">
      <p>{{ loadError }}</p>
      <button class="btn-retry" @click="fetchProject">Retry</button>
    </div>

    <template v-else>

      <div v-if="apiError" class="alert alert-error">
        <span>⚠ {{ apiError }}</span>
        <button @click="apiError = null" class="alert-close">✕</button>
      </div>

      <div class="form-container">
        <form @submit.prevent="handleSubmit">

          <!-- Basic Information -->
          <div class="form-section">
            <h2 class="section-title">Basic Information</h2>
            <div class="form-grid">

              <div class="form-group full-width" :class="{ 'has-error': errors.title }">
                <label for="title">Project Title <span class="required">*</span></label>
                <input id="title" v-model="form.title" type="text" placeholder="e.g., Dell Inspiron 15 Complete Overhaul" @blur="validateField('title')" />
                <span v-if="errors.title" class="field-error">{{ errors.title }}</span>
              </div>

              <div class="form-group full-width" :class="{ 'has-error': errors.description }">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" v-model="form.description" rows="4" placeholder="Describe the project in detail..." @blur="validateField('description')"></textarea>
                <div class="char-count-row">
                  <span v-if="errors.description" class="field-error">{{ errors.description }}</span>
                  <span class="char-count" :class="{ 'over-limit': form.description.length > 500 }">{{ form.description.length }} / 500</span>
                </div>
              </div>

              <div class="form-group">
                <label for="status">Status</label>
                <select id="status" v-model="form.status">
                  <option value="published">Published</option>
                  <option value="draft">Draft</option>
                </select>
              </div>

              <div class="form-group">
                <label for="order">Display Order</label>
                <input id="order" v-model.number="form.order" type="number" min="0" placeholder="0" />
              </div>

            </div>
          </div>

          <!-- Thumbnail -->
          <div class="form-section">
            <h2 class="section-title">Project Thumbnail</h2>
            <div
              class="image-upload-area"
              :class="{ 'has-error': errors.thumbnail, 'has-image': imagePreview }"
              @click="fileInput.click()"
            >
              <img v-if="imagePreview" :src="imagePreview" alt="Preview" class="image-preview" />
              <div v-else class="upload-placeholder">
                <img src="https://placehold.co/48x48" alt="Upload" class="upload-icon" />
                <p>Click to change thumbnail</p>
                <span>PNG, JPG, WebP — max 2MB, recommended 380x220</span>
              </div>
            </div>
            <span v-if="errors.thumbnail" class="field-error mt-4">{{ errors.thumbnail }}</span>
            <button v-if="imagePreview" type="button" class="btn-remove-image" @click.stop="removeImage">✕ Remove image</button>
            <input ref="fileInput" type="file" accept="image/png, image/jpeg, image/webp" class="hidden-input" @change="handleImageUpload" />
          </div>

          <!-- Services -->
          <div class="form-section">
            <h2 class="section-title">Services Involved <span class="optional">(at least one)</span></h2>
            <div class="services-list">
              <div v-for="(service, index) in form.services" :key="index" class="service-row">
                <select v-model="form.services[index].name" class="service-select">
                  <option value="" disabled>Select service</option>
                  <option value="Repair">Repair</option>
                  <option value="Installation">Installation</option>
                  <option value="Maintenance">Maintenance</option>
                  <option value="Upgrade">Upgrade</option>
                </select>
                <select v-model="form.services[index].color" class="color-select">
                  <option value="" disabled>Badge color</option>
                  <option value="red">Red</option>
                  <option value="blue">Blue</option>
                  <option value="yellow">Yellow</option>
                  <option value="green">Green</option>
                </select>
                <div class="badge-preview service-badge" :class="service.color" v-if="service.name">
                  <span class="badge-dot"></span>
                  {{ service.name }}
                </div>
                <button type="button" class="btn-remove" @click="removeService(index)">Remove</button>
              </div>
            </div>
            <span v-if="errors.services" class="field-error">{{ errors.services }}</span>
            <button type="button" class="btn-add-service" @click="addService">+ Add Service</button>
          </div>

          <div class="form-actions">
            <router-link to="/admin/portfolio" class="btn-secondary">Cancel</router-link>
            <button type="submit" class="btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner"></span>
              {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>

        </form>
      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios, { baseURL } from '@/api/axios';

const router = useRouter();
const route  = useRoute();

const form = ref({
  title:       '',
  description: '',
  status:      'published',
  order:       0,
  thumbnail:   null,
  services:    [],
});

const errors        = reactive({});
const apiError      = ref(null);
const loadError     = ref(null);
const imagePreview  = ref(null);
const existingImage = ref(null);
const fileInput     = ref(null);
const isLoading     = ref(true);
const isSubmitting  = ref(false);

// ── Fetch ─────────────────────────────────────────────────
const fetchProject = async () => {
  isLoading.value = true;
  loadError.value = null;
  try {
    const { data } = await axios.get(`/admin/portfolio/${route.params.id}`);
    const p = data.data;
    form.value = {
      title:       p.title       ?? '',
      description: p.description ?? '',
      status:      p.status      ?? 'published',
      order:       p.order       ?? 0,
      thumbnail:   null,
      services:    p.services    ?? [],
    };
    if (p.thumbnail) {
      existingImage.value = p.thumbnail;
      imagePreview.value  = `${baseURL}/storage/${p.thumbnail}`;
    }
  } catch (err) {
    loadError.value = 'Failed to load project. Please try again.';
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

// ── Image ─────────────────────────────────────────────────
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_BYTES     = 2 * 1024 * 1024;

const handleImageUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  if (!ALLOWED_TYPES.includes(file.type)) {
    errors.thumbnail = 'Only JPG, PNG, or WebP images are allowed.';
    fileInput.value.value = '';
    return;
  }
  if (file.size > MAX_BYTES) {
    errors.thumbnail = `Image must be under 2MB (yours is ${(file.size / 1024 / 1024).toFixed(1)}MB).`;
    fileInput.value.value = '';
    return;
  }
  delete errors.thumbnail;
  form.value.thumbnail = file;
  imagePreview.value   = URL.createObjectURL(file);
};

const removeImage = () => {
  form.value.thumbnail = null;
  imagePreview.value   = null;
  existingImage.value  = null;
  fileInput.value.value = '';
  delete errors.thumbnail;
};

// ── Services ──────────────────────────────────────────────
const addService    = () => form.value.services.push({ name: '', color: 'red' });
const removeService = (i) => form.value.services.splice(i, 1);

// ── Validation ────────────────────────────────────────────
const validateField = (field) => {
  switch (field) {
    case 'title':
      if (!form.value.title.trim())               errors.title = 'Project title is required.';
      else if (form.value.title.trim().length < 5) errors.title = 'Title must be at least 5 characters.';
      else delete errors.title;
      break;
    case 'description':
      if (!form.value.description.trim())           errors.description = 'Description is required.';
      else if (form.value.description.length > 500)  errors.description = 'Description cannot exceed 500 characters.';
      else delete errors.description;
      break;
  }
};

const validateAll = () => {
  validateField('title');
  validateField('description');
  const hasValid = form.value.services.some(s => s.name.trim());
  if (!hasValid) errors.services = 'Please add at least one service.';
  else delete errors.services;
  return Object.keys(errors).length === 0;
};

// ── Submit ────────────────────────────────────────────────
const handleSubmit = async () => {
  apiError.value = null;
  if (!validateAll()) return;

  isSubmitting.value = true;
  try {
    const fd = new FormData();
    fd.append('_method',     'PUT');
    fd.append('title',       form.value.title.trim());
    fd.append('description', form.value.description.trim());
    fd.append('status',      form.value.status);
    fd.append('order',       form.value.order);

    if (form.value.thumbnail) {
      fd.append('thumbnail', form.value.thumbnail);
    } else if (!existingImage.value) {
      fd.append('remove_thumbnail', 1);
    }

    const validServices = form.value.services.filter(s => s.name.trim());
    fd.append('services', JSON.stringify(validServices));

    await axios.post(`/admin/portfolio/${route.params.id}`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    router.push('/admin/portfolio');
  } catch (err) {
    if (err.response?.status === 422) {
      Object.entries(err.response.data.errors).forEach(([key, msgs]) => {
        errors[key] = msgs[0];
      });
    } else {
      apiError.value = err.response?.data?.message || 'Something went wrong. Please try again.';
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(fetchProject);
</script>

<style scoped>
.portfolio-form-page { padding: 2rem; max-width: 900px; margin: 0 auto; }

.page-header { margin-bottom: 2rem; }
.back-link { display: inline-block; color: #666; text-decoration: none; font-size: 0.9rem; margin-bottom: 0.75rem; transition: color 0.2s ease; }
.back-link:hover { color: #00805c; }
.page-header h1 { font-size: 2rem; color: #1a1a1a; margin: 0 0 0.25rem 0; }
.subtitle { color: #666; margin: 0; font-size: 0.95rem; }

.loading-state, .error-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 4rem 2rem; color: #666; text-align: center; }
.error-state { color: #991b1b; }
.btn-retry { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.9rem; }
.btn-retry:hover { border-color: #00ff88; }
.spinner-lg { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #00ff88; border-radius: 50%; animation: spin 0.7s linear infinite; }

.alert { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500; }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.alert-close { background: none; border: none; font-size: 1rem; cursor: pointer; color: inherit; opacity: 0.7; }
.alert-close:hover { opacity: 1; }

.form-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.form-section { padding: 2rem; border-bottom: 1px solid #f3f4f6; }
.form-section:last-of-type { border-bottom: none; }
.section-title { font-size: 1.1rem; font-weight: 600; color: #1a1a1a; margin: 0 0 1.5rem 0; }
.optional { font-size: 0.85rem; color: #666; font-weight: 400; }

.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group label { font-weight: 600; font-size: 0.9rem; color: #1a1a1a; }
.required { color: #ef4444; }

.form-group input,
.form-group select,
.form-group textarea { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: border-color 0.3s ease; }
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { outline: none; border-color: #00ff88; box-shadow: 0 0 0 3px rgba(0,255,136,0.1); }
.form-group textarea { resize: vertical; min-height: 100px; }

.has-error input, .has-error textarea { border-color: #ef4444 !important; }
.field-error { font-size: 0.82rem; color: #ef4444; font-weight: 500; display: block; }
.mt-4 { margin-top: 0.5rem; }
.char-count-row { display: flex; justify-content: space-between; align-items: center; min-height: 1.2rem; }
.char-count { font-size: 0.85rem; color: #666; margin-left: auto; }
.char-count.over-limit { color: #ef4444; font-weight: 600; }

.image-upload-area { border: 2px dashed #e5e7eb; border-radius: 8px; padding: 2rem; cursor: pointer; transition: border-color 0.3s ease; min-height: 160px; display: flex; align-items: center; justify-content: center; }
.image-upload-area:hover, .image-upload-area.has-image { border-color: #00ff88; }
.image-upload-area.has-error { border-color: #ef4444; }
.upload-placeholder { text-align: center; color: #666; }
.upload-icon { width: 48px; height: 48px; margin-bottom: 0.75rem; opacity: 0.5; }
.upload-placeholder p { margin: 0 0 0.25rem 0; font-weight: 500; color: #1a1a1a; }
.upload-placeholder span { font-size: 0.85rem; }
.image-preview { max-width: 100%; max-height: 220px; object-fit: contain; border-radius: 8px; }
.hidden-input { display: none; }
.btn-remove-image { margin-top: 0.6rem; background: none; border: none; color: #ef4444; font-size: 0.85rem; cursor: pointer; padding: 0; display: block; }
.btn-remove-image:hover { text-decoration: underline; }

.services-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem; }
.service-row { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
.service-select, .color-select { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; font-family: inherit; background: white; cursor: pointer; }
.service-select { flex: 1; min-width: 150px; }
.color-select { width: 130px; }
.service-select:focus, .color-select:focus { outline: none; border-color: #00ff88; }

.badge-preview { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1.5px solid; white-space: nowrap; }
.badge-dot { width: 6px; height: 6px; border-radius: 50%; }
.service-badge.yellow { background: #fff9e6; border-color: #ffd93d; color: #b8860b; }
.service-badge.yellow .badge-dot { background: #ffd93d; }
.service-badge.red { background: #ffe6e6; border-color: #ff6b6b; color: #c92a2a; }
.service-badge.red .badge-dot { background: #ff6b6b; }
.service-badge.blue { background: #e6f7ff; border-color: #40a9ff; color: #096dd9; }
.service-badge.blue .badge-dot { background: #40a9ff; }
.service-badge.green { background: #e6fcf3; border-color: #00ff88; color: #00805c; }
.service-badge.green .badge-dot { background: #00ff88; }

.btn-remove { background: #fee2e2; color: #ef4444; border: none; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; }
.btn-remove:hover { background: #ef4444; color: white; }
.btn-add-service { background: #f0fdf7; color: #00805c; border: 1px dashed #00ff88; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.9rem; font-weight: 500; cursor: pointer; width: 100%; transition: all 0.3s ease; margin-top: 0.5rem; }
.btn-add-service:hover { background: #e6fcf3; }

.form-actions { display: flex; justify-content: flex-end; gap: 1rem; padding: 2rem; border-top: 1px solid #e5e7eb; }
.btn-secondary { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; background: #f3f4f6; color: #666; border: none; transition: all 0.3s ease; }
.btn-secondary:hover { background: #e5e7eb; color: #1a1a1a; }
.btn-primary { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; border: none; background: linear-gradient(135deg, #00ff88 0%, #00cc6f 100%); color: #0a1f1a; box-shadow: 0 2px 8px rgba(0,255,136,0.3); transition: all 0.3s ease; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,255,136,0.4); }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.spinner { width: 14px; height: 14px; border: 2px solid rgba(10,31,26,0.3); border-top-color: #0a1f1a; border-radius: 50%; animation: spin 0.6s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 768px) {
  .portfolio-form-page { padding: 1rem; }
  .form-grid { grid-template-columns: 1fr; }
  .form-section { padding: 1.5rem 1rem; }
  .service-row { flex-direction: column; align-items: stretch; }
  .color-select { width: 100%; }
  .form-actions { padding: 1.5rem 1rem; flex-direction: column-reverse; }
  .btn-secondary, .btn-primary { width: 100%; text-align: center; justify-content: center; }
}
</style>