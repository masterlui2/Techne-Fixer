<template>
  <div class="modal-overlay" @click.self="closeModal">
    <div class="modal-container">
      <div class="modal-header">
        <h2>{{ isEditMode ? 'Edit Service' : 'Add New Service' }}</h2>
        <button class="close-btn" @click="closeModal">×</button>
      </div>

      <form @submit.prevent="handleSubmit" class="modal-body">
        <div class="form-grid">
          <!-- Service Name -->
          <div class="form-group full-width">
            <label for="name" class="form-label">
              Service Name <span class="required">*</span>
            </label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              class="form-input"
              placeholder="e.g., Computer Repair"
              required
            />
          </div>

          <!-- Icon -->
          <div class="form-group">
            <label for="icon" class="form-label">
              Icon (Emoji) <span class="required">*</span>
            </label>
            <input
              id="icon"
              v-model="formData.icon"
              type="text"
              class="form-input icon-input"
              placeholder="💻"
              maxlength="2"
              required
            />
            <div class="icon-preview">{{ formData.icon || '❓' }}</div>
          </div>

          <!-- Category -->
          <div class="form-group">
            <label for="category" class="form-label">
              Category <span class="required">*</span>
            </label>
            <select
              id="category"
              v-model="formData.category"
              class="form-input"
              required
            >
              <option value="" disabled>Select a category</option>
              <option value="Hardware">Hardware</option>
              <option value="Software">Software</option>
              <option value="Network">Network</option>
              <option value="Data">Data</option>
              <option value="Security">Security</option>
              <option value="Consulting">Consulting</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <!-- Price -->
          <div class="form-group">
            <label for="price" class="form-label">
              Price (USD) <span class="required">*</span>
            </label>
            <div class="input-group">
              <span class="input-prefix">$</span>
              <input
                id="price"
                v-model.number="formData.price"
                type="number"
                class="form-input with-prefix"
                placeholder="0.00"
                step="0.01"
                min="0"
                required
              />
            </div>
          </div>

          <!-- Status -->
          <div class="form-group">
            <label for="status" class="form-label">
              Status <span class="required">*</span>
            </label>
            <select
              id="status"
              v-model="formData.status"
              class="form-input"
              required
            >
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <!-- Description -->
          <div class="form-group full-width">
            <label for="description" class="form-label">
              Description <span class="required">*</span>
            </label>
            <textarea
              id="description"
              v-model="formData.description"
              class="form-textarea"
              rows="4"
              placeholder="Describe the service in detail..."
              required
            ></textarea>
            <div class="char-count">
              {{ formData.description.length }} / 500 characters
            </div>
          </div>

          <!-- Features -->
          <div class="form-group full-width">
            <label class="form-label">
              Service Features
              <span class="optional">(Optional)</span>
            </label>
            <div class="features-list">
              <div
                v-for="(feature, index) in formData.features"
                :key="index"
                class="feature-item"
              >
                <input
                  v-model="formData.features[index]"
                  type="text"
                  class="form-input"
                  placeholder="Feature description"
                />
                <button
                  type="button"
                  class="btn-remove"
                  @click="removeFeature(index)"
                  title="Remove feature"
                >
                  ×
                </button>
              </div>
            </div>
            <button
              type="button"
              class="btn-add-feature"
              @click="addFeature"
            >
              + Add Feature
            </button>
          </div>

          <!-- Featured Toggle -->
          <div class="form-group full-width">
            <label class="checkbox-label">
              <input
                v-model="formData.featured"
                type="checkbox"
                class="checkbox-input"
              />
              <span class="checkbox-text">
                <span class="checkbox-title">Mark as Featured</span>
                <span class="checkbox-desc">Featured services appear prominently on the homepage</span>
              </span>
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" @click="closeModal">
            Cancel
          </button>
          <button type="submit" class="btn-primary">
            {{ isEditMode ? 'Update Service' : 'Create Service' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  service: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'save']);

const isEditMode = computed(() => !!props.service);

const formData = ref({
  name: '',
  icon: '',
  category: '',
  price: 0,
  status: 'Active',
  description: '',
  features: [],
  featured: false
});

onMounted(() => {
  if (props.service) {
    formData.value = {
      ...props.service,
      features: props.service.features || []
    };
  }
});

const addFeature = () => {
  formData.value.features.push('');
};

const removeFeature = (index) => {
  formData.value.features.splice(index, 1);
};

const closeModal = () => {
  emit('close');
};

const handleSubmit = () => {
  // Filter out empty features
  const cleanedData = {
    ...formData.value,
    features: formData.value.features.filter(f => f.trim() !== '')
  };
  
  emit('save', cleanedData);
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-container {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  animation: slideUp 0.3s ease;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #1a1a1a;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 2rem;
  color: #666;
  cursor: pointer;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  line-height: 1;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #1a1a1a;
}

.modal-body {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.required {
  color: #ef4444;
}

.optional {
  color: #666;
  font-weight: 400;
  font-size: 0.85rem;
}

.form-input,
.form-textarea {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #00ff88;
  box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.icon-input {
  text-align: center;
  font-size: 1.5rem;
}

.icon-preview {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 2rem;
  pointer-events: none;
}

.form-group:has(.icon-input) {
  position: relative;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-prefix {
  position: absolute;
  left: 1rem;
  color: #666;
  font-weight: 600;
}

.with-prefix {
  padding-left: 2rem;
}

.char-count {
  margin-top: 0.5rem;
  font-size: 0.85rem;
  color: #666;
  text-align: right;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.feature-item {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.feature-item .form-input {
  flex: 1;
}

.btn-remove {
  background: #fee2e2;
  color: #ef4444;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  flex-shrink: 0;
  line-height: 1;
}

.btn-remove:hover {
  background: #ef4444;
  color: white;
}

.btn-add-feature {
  background: #f0fdf7;
  color: #00805c;
  border: 1px dashed #00ff88;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

.btn-add-feature:hover {
  background: #e6fcf3;
  border-color: #00cc6f;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  cursor: pointer;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  transition: background 0.3s ease;
}

.checkbox-label:hover {
  background: #f3f4f6;
}

.checkbox-input {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: #00ff88;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-text {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.checkbox-title {
  font-weight: 600;
  color: #1a1a1a;
}

.checkbox-desc {
  font-size: 0.85rem;
  color: #666;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e5e7eb;
}

.btn-secondary,
.btn-primary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-secondary {
  background: #f3f4f6;
  color: #666;
}

.btn-secondary:hover {
  background: #e5e7eb;
  color: #1a1a1a;
}

.btn-primary {
  background: linear-gradient(135deg, #00ff88 0%, #00cc6f 100%);
  color: #0a1f1a;
  box-shadow: 0 2px 8px rgba(0, 255, 136, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 255, 136, 0.4);
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .modal-container {
    max-width: 100%;
    max-height: 100vh;
    border-radius: 0;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 1.5rem 1rem;
  }
}
</style>