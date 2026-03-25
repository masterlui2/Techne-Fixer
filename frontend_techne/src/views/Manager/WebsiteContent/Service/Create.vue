<template>
  <div class="service-form-page">

    <div class="page-header">
      <router-link to="/admin/services" class="back-link">← Back to Services</router-link>
      <h1>Add New Service</h1>
      <p class="subtitle">Fill in the details to create a new service</p>
    </div>

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

            <div class="form-group full-width" :class="{ 'has-error': errors.name }">
              <label for="name">Service Name <span class="required">*</span></label>
              <input id="name" v-model="form.name" type="text" placeholder="e.g., Laptop Repair & Upgrade" @blur="validateField('name')" />
              <span v-if="errors.name" class="field-error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="subtitle">Subtitle</label>
              <input id="subtitle" v-model="form.subtitle" type="text" placeholder="e.g., Fast & reliable repairs" />
            </div>

            <div class="form-group" :class="{ 'has-error': errors.category_id }">
              <label for="category">Category <span class="required">*</span></label>
              <select id="category" v-model="form.category_id" @change="validateField('category_id')">
                <option value="" disabled>
                  {{ categoriesLoading ? 'Loading...' : categories.length ? 'Select a category' : 'No categories found' }}
                </option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
              <span v-if="errors.category_id" class="field-error">{{ errors.category_id }}</span>
            </div>

            <div class="form-group">
              <label for="status">Status <span class="required">*</span></label>
              <select id="status" v-model="form.status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
              </select>
            </div>

            <div class="form-group">
              <label for="order">Display Order</label>
              <input id="order" v-model.number="form.order" type="number" min="0" placeholder="0" />
            </div>

            <div class="form-group">
              <label for="icon">Icon Class</label>
              <input id="icon" v-model="form.icon" type="text" placeholder="e.g., fa-wrench" />
            </div>

            <div class="form-group full-width" :class="{ 'has-error': errors.description }">
              <label for="description">Short Description <span class="required">*</span></label>
              <textarea id="description" v-model="form.description" rows="3" placeholder="Brief summary (max 500 chars)..." @blur="validateField('description')"></textarea>
              <div class="char-count-row">
                <span v-if="errors.description" class="field-error">{{ errors.description }}</span>
                <span class="char-count" :class="{ 'over-limit': form.description.length > 500 }">
                  {{ form.description.length }} / 500
                </span>
              </div>
            </div>

            <div class="form-group full-width">
              <label for="long_description">Full Description</label>
              <textarea id="long_description" v-model="form.long_description" rows="5" placeholder="Detailed description..."></textarea>
            </div>

          </div>
        </div>

        <!-- Image Upload -->
        <div class="form-section">
          <h2 class="section-title">Service Image</h2>
          <div
            class="image-upload-area"
            :class="{ 'has-error': errors.image, 'has-image': imagePreview }"
            @click="fileInput.click()"
          >
            <img v-if="imagePreview" :src="imagePreview" alt="Preview" class="image-preview" />
            <div v-else class="upload-placeholder">
              <img src="https://placehold.co/60x60" alt="Upload" class="upload-icon" />
              <p>Click to upload image</p>
              <span>PNG, JPG, WebP — max 2MB</span>
            </div>
          </div>
          <span v-if="errors.image" class="field-error mt-4">{{ errors.image }}</span>
          <button v-if="imagePreview" type="button" class="btn-remove-image" @click.stop="removeImage">
            ✕ Remove image
          </button>
          <input ref="fileInput" type="file" accept="image/png, image/jpeg, image/webp" class="hidden-input" @change="handleImageUpload" />
        </div>

        <!-- Service Types & Scope of Works -->
        <div class="form-section">
          <h2 class="section-title">
            Service Types & Scope of Works
            <span class="optional">(select types, then add scope items per type)</span>
          </h2>

          <div v-if="serviceTypesLoading" class="types-loading">Loading service types...</div>

          <div v-else-if="availableTypes.length === 0" class="types-empty">
            No service types found. Please add some first.
          </div>

          <div v-else class="service-types-builder">
            <div
              v-for="type in availableTypes"
              :key="type.id"
              class="type-block"
              :class="{ 'is-selected': isTypeSelected(type.id) }"
            >
              <!-- Type Header -->
              <div class="type-header" @click="toggleType(type)">
                <div class="type-header-left">
                  <div class="type-checkbox" :class="{ checked: isTypeSelected(type.id) }">
                    <span v-if="isTypeSelected(type.id)">✓</span>
                  </div>
                  <span class="type-dot" :style="{ background: type.color || '#00ff88' }"></span>
                  <span class="type-name">{{ type.name }}</span>
                </div>
                <span class="type-count" v-if="isTypeSelected(type.id)">
                  {{ getScopesForType(type.id).length }} item(s)
                </span>
              </div>

              <!-- Scope Items -->
              <div class="type-scopes" v-if="isTypeSelected(type.id)">
                <div
                  v-for="(_, idx) in getScopesForType(type.id)"
                  :key="idx"
                  class="scope-row"
                >
                  <input
                    v-model="form.selectedTypes[type.id][idx]"
                    type="text"
                    :placeholder="`e.g., ${type.name} scope item ${idx + 1}`"
                  />
                  <button type="button" class="btn-remove" @click="removeScopeItem(type.id, idx)">
                    Remove
                  </button>
                </div>
                <button type="button" class="btn-add-scope" @click="addScopeItem(type.id)">
                  + Add scope item
                </button>
              </div>
            </div>
          </div>

          <span v-if="errors.service_types" class="field-error mt-4">{{ errors.service_types }}</span>
        </div>

        <!-- Featured Toggle -->
        <div class="form-section">
          <label class="checkbox-label">
            <input v-model="form.featured" type="checkbox" />
            <div class="checkbox-text">
              <span class="checkbox-title">Mark as Featured</span>
              <span class="checkbox-desc">Featured services appear prominently on the homepage</span>
            </div>
          </label>
        </div>

        <div class="form-actions">
          <router-link to="/admin/services" class="btn-secondary">Cancel</router-link>
          <button type="submit" class="btn-primary" :disabled="isSubmitting">
            <span v-if="isSubmitting" class="spinner"></span>
            {{ isSubmitting ? 'Creating...' : 'Create Service' }}
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '@/api/axios';

const router = useRouter();

const form = ref({
  name:             '',
  subtitle:         '',
  category_id:      '',
  status:           'active',
  order:            0,
  icon:             '',
  description:      '',
  long_description: '',
  image:            null,
  selectedTypes:    {}, // { [typeId]: string[] }
  featured:         false,
});

const errors              = reactive({});
const apiError            = ref(null);
const imagePreview        = ref(null);
const fileInput           = ref(null);
const isSubmitting        = ref(false);
const categories          = ref([]);
const categoriesLoading   = ref(false);
const availableTypes      = ref([]);
const serviceTypesLoading = ref(false);

// ── Categories ────────────────────────────────────────────
const fetchCategories = async () => {
  categoriesLoading.value = true;
  try {
    const { data } = await axios.get('/admin/categories');
    categories.value = data.data ?? data;
  } catch (err) {
    console.error('Failed to load categories:', err);
    apiError.value = 'Could not load categories. Please refresh the page.';
  } finally {
    categoriesLoading.value = false;
  }
};

// ── Service Types ─────────────────────────────────────────
const fetchServiceTypes = async () => {
  serviceTypesLoading.value = true;
  try {
    const { data } = await axios.get('/admin/service-types');
    availableTypes.value = data.data;
  } catch (err) {
    console.error('Failed to load service types:', err);
  } finally {
    serviceTypesLoading.value = false;
  }
};

const isTypeSelected   = (id) => id in form.value.selectedTypes;
const getScopesForType = (id) => form.value.selectedTypes[id] ?? [];

const toggleType = (type) => {
  if (isTypeSelected(type.id)) {
    const updated = { ...form.value.selectedTypes };
    delete updated[type.id];
    form.value.selectedTypes = updated;
  } else {
    form.value.selectedTypes = { ...form.value.selectedTypes, [type.id]: [] };
  }
};

const addScopeItem = (typeId) => {
  form.value.selectedTypes[typeId].push('');
};

const removeScopeItem = (typeId, idx) => {
  form.value.selectedTypes[typeId].splice(idx, 1);
};

// ── Image ─────────────────────────────────────────────────
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_BYTES     = 2 * 1024 * 1024;

const handleImageUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  if (!ALLOWED_TYPES.includes(file.type)) {
    errors.image = 'Only JPG, PNG, or WebP images are allowed.';
    fileInput.value.value = '';
    return;
  }
  if (file.size > MAX_BYTES) {
    errors.image = `Image must be under 2MB (yours is ${(file.size / 1024 / 1024).toFixed(1)}MB).`;
    fileInput.value.value = '';
    return;
  }
  delete errors.image;
  form.value.image   = file;
  imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
  form.value.image   = null;
  imagePreview.value = null;
  fileInput.value.value = '';
  delete errors.image;
};

// ── Validation ────────────────────────────────────────────
const validateField = (field) => {
  switch (field) {
    case 'name':
      if (!form.value.name.trim())               errors.name = 'Service name is required.';
      else if (form.value.name.trim().length < 3) errors.name = 'Name must be at least 3 characters.';
      else delete errors.name;
      break;
    case 'category_id':
      if (!form.value.category_id) errors.category_id = 'Please select a category.';
      else delete errors.category_id;
      break;
    case 'description':
      if (!form.value.description.trim())           errors.description = 'Description is required.';
      else if (form.value.description.length > 500)  errors.description = 'Description cannot exceed 500 characters.';
      else delete errors.description;
      break;
  }
};

const validateAll = () => {
  validateField('name');
  validateField('category_id');
  validateField('description');
  return Object.keys(errors).length === 0;
};

// ── Submit ────────────────────────────────────────────────
const handleSubmit = async () => {
  apiError.value = null;
  if (!validateAll()) return;

  isSubmitting.value = true;
  try {
    const fd = new FormData();
    fd.append('name',             form.value.name.trim());
    fd.append('subtitle',         form.value.subtitle.trim());
    fd.append('category_id',      form.value.category_id);
    fd.append('status',           form.value.status);
    fd.append('order',            form.value.order);
    fd.append('icon',             form.value.icon.trim());
    fd.append('description',      form.value.description.trim());
    fd.append('long_description', form.value.long_description.trim());
    fd.append('featured',         form.value.featured ? 1 : 0);

    if (form.value.image) fd.append('image', form.value.image);

    // Serialize selectedTypes → service_types[i][id] + service_types[i][scopes][j]
    Object.entries(form.value.selectedTypes).forEach(([typeId, scopes], i) => {
      fd.append(`service_types[${i}][id]`, typeId);
      scopes
        .filter(s => s.trim())
        .forEach((s, j) => fd.append(`service_types[${i}][scopes][${j}]`, s));
    });

    await axios.post('/admin/services', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    router.push('/admin/services');

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

onMounted(async () => {
  await Promise.all([fetchCategories(), fetchServiceTypes()]);
});
</script>

<style scoped>
.service-form-page { padding: 2rem; max-width: 900px; margin: 0 auto; }

.page-header { margin-bottom: 2rem; }
.back-link { display: inline-block; color: #666; text-decoration: none; font-size: 0.9rem; margin-bottom: 0.75rem; transition: color 0.2s ease; }
.back-link:hover { color: #00805c; }
.page-header h1 { font-size: 2rem; color: #1a1a1a; margin: 0 0 0.25rem 0; }
.subtitle { color: #666; margin: 0; font-size: 0.95rem; }

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

.has-error input, .has-error select, .has-error textarea { border-color: #ef4444 !important; }
.has-error input:focus, .has-error select:focus, .has-error textarea:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important; }
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
.image-preview { max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 8px; }
.hidden-input { display: none; }
.btn-remove-image { margin-top: 0.6rem; background: none; border: none; color: #ef4444; font-size: 0.85rem; cursor: pointer; padding: 0; display: block; }
.btn-remove-image:hover { text-decoration: underline; }

/* Service Types Builder */
.service-types-builder { display: flex; flex-direction: column; gap: 0.75rem; }
.type-block { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; transition: border-color 0.2s ease; }
.type-block.is-selected { border-color: #00ff88; }
.type-header { display: flex; justify-content: space-between; align-items: center; padding: 0.875rem 1rem; cursor: pointer; background: #f9fafb; transition: background 0.2s ease; user-select: none; }
.type-block.is-selected .type-header { background: #f0fdf7; }
.type-header:hover { background: #e6fcf3; }
.type-header-left { display: flex; align-items: center; gap: 0.75rem; }
.type-checkbox { width: 20px; height: 20px; border: 2px solid #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; transition: all 0.2s ease; flex-shrink: 0; }
.type-checkbox.checked { background: #00ff88; border-color: #00ff88; color: #0a1f1a; }
.type-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.type-name { font-weight: 600; font-size: 0.95rem; color: #1a1a1a; }
.type-count { font-size: 0.82rem; color: #00805c; font-weight: 500; }
.type-scopes { padding: 1rem; border-top: 1px solid #e5e7eb; display: flex; flex-direction: column; gap: 0.5rem; background: white; }
.scope-row { display: flex; gap: 0.5rem; align-items: center; }
.scope-row input { flex: 1; padding: 0.65rem 0.875rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; font-family: inherit; }
.scope-row input:focus { outline: none; border-color: #00ff88; box-shadow: 0 0 0 3px rgba(0,255,136,0.1); }
.btn-remove { background: #fee2e2; color: #ef4444; border: none; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; }
.btn-remove:hover { background: #ef4444; color: white; }
.btn-add-scope { background: #f0fdf7; color: #00805c; border: 1px dashed #00ff88; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; width: 100%; margin-top: 0.25rem; transition: all 0.2s ease; }
.btn-add-scope:hover { background: #e6fcf3; }
.types-loading, .types-empty { padding: 1.5rem; text-align: center; color: #666; font-size: 0.9rem; border: 1px dashed #e5e7eb; border-radius: 8px; }

.checkbox-label { display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; padding: 1rem; background: #f9fafb; border-radius: 8px; }
.checkbox-label input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; accent-color: #00ff88; flex-shrink: 0; margin-top: 2px; }
.checkbox-text { display: flex; flex-direction: column; gap: 0.25rem; }
.checkbox-title { font-weight: 600; color: #1a1a1a; }
.checkbox-desc { font-size: 0.85rem; color: #666; }

.form-actions { display: flex; justify-content: flex-end; gap: 1rem; padding: 2rem; border-top: 1px solid #e5e7eb; }
.btn-secondary { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; background: #f3f4f6; color: #666; border: none; transition: all 0.3s ease; }
.btn-secondary:hover { background: #e5e7eb; color: #1a1a1a; }
.btn-primary { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; border: none; background: linear-gradient(135deg, #00ff88 0%, #00cc6f 100%); color: #0a1f1a; box-shadow: 0 2px 8px rgba(0,255,136,0.3); transition: all 0.3s ease; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,255,136,0.4); }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner { width: 14px; height: 14px; border: 2px solid rgba(10,31,26,0.3); border-top-color: #0a1f1a; border-radius: 50%; animation: spin 0.6s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 768px) {
  .service-form-page { padding: 1rem; }
  .form-grid { grid-template-columns: 1fr; }
  .form-section { padding: 1.5rem 1rem; }
  .form-actions { padding: 1.5rem 1rem; flex-direction: column-reverse; }
  .btn-secondary, .btn-primary { width: 100%; text-align: center; justify-content: center; }
}
</style>