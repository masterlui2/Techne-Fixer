<template>
  <div class="modal-overlay" @click.self="handleCancel">
    <div class="confirm-modal" :class="type">
      <div class="modal-icon">
        <span v-if="type === 'danger'">⚠️</span>
        <span v-else-if="type === 'warning'">⚡</span>
        <span v-else>ℹ️</span>
      </div>

      <h3 class="modal-title">{{ title }}</h3>
      <p class="modal-message">{{ message }}</p>

      <div class="modal-actions">
        <button class="btn btn-cancel" @click="handleCancel">
          {{ cancelText }}
        </button>
        <button class="btn btn-confirm" :class="type" @click="handleConfirm">
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'warning', 'danger'].includes(value)
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const handleConfirm = () => {
  emit('confirm');
};

const handleCancel = () => {
  emit('cancel');
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
  z-index: 10000;
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

.confirm-modal {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  max-width: 450px;
  width: 100%;
  text-align: center;
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

.modal-icon {
  font-size: 3.5rem;
  margin-bottom: 1rem;
}

.modal-title {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  color: #1a1a1a;
}

.modal-message {
  margin: 0 0 2rem 0;
  color: #666;
  font-size: 1rem;
  line-height: 1.6;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.btn {
  padding: 0.75rem 1.75rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  min-width: 120px;
}

.btn-cancel {
  background: #f3f4f6;
  color: #666;
}

.btn-cancel:hover {
  background: #e5e7eb;
  color: #1a1a1a;
}

.btn-confirm {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-confirm.info {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.btn-confirm.info:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-confirm.warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
}

.btn-confirm.warning:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.btn-confirm.danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.btn-confirm.danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .confirm-modal {
    padding: 1.5rem;
  }

  .modal-actions {
    flex-direction: column-reverse;
  }

  .btn {
    width: 100%;
  }
}
</style>