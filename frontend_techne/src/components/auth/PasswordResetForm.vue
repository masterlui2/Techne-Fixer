<!-- src/components/Auth/ForgotPassword.vue -->
<template>
  <div class="forgot-password-card">
    <!-- Logo -->
    <div class="logo-section">
      <img :src="logo" alt="Techne-Fixer" class="logo" />
      <h1>Reset Password</h1>
      <p v-if="!emailSent">Enter your email to receive a password reset link</p>
      <p v-else class="success-text">Check your email for reset instructions</p>
    </div>

    <!-- Success State -->
    <div v-if="emailSent" class="success-state">
      <div class="success-icon">
        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
          <circle cx="30" cy="30" r="28" stroke="#558b2f" stroke-width="3"/>
          <path d="M17 30l8 8 18-18" stroke="#558b2f" stroke-width="3" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="success-message">
        We've sent a password reset link to <strong>{{ email }}</strong>
      </p>
      <p class="success-info">
        Please check your inbox and spam folder. The link will expire in 24 hours.
      </p>
      
      <button @click="resetForm" class="secondary-btn">
        Send another email
      </button>
      
      <div class="login-link">
        <router-link to="/login">Back to sign in</router-link>
      </div>
    </div>

    <!-- Reset Form -->
    <form v-else @submit.prevent="handleReset" class="reset-form">
      <!-- Email Input -->
      <div class="form-group">
        <label for="email">Email Address</label>
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="your.email@example.com"
          required
          autofocus
        />
      </div>

      <!-- Info Box -->
      <div class="info-box">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5"/>
          <path d="M9 5v4M9 11v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span>We'll send you a secure link to reset your password</span>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="submit-btn" :disabled="isLoading">
        <span v-if="!isLoading">Send Reset Link</span>
        <span v-else class="loading">
          <span class="spinner"></span>
          Sending...
        </span>
      </button>

      <!-- Back to Login -->
      <div class="login-link">
        <router-link to="/login">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Back to sign in
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import logo from '@/assets/images/logo.png';

// Form state
const email = ref('');
const isLoading = ref(false);
const emailSent = ref(false);

// Handle password reset
const handleReset = async () => {
  isLoading.value = true;
  
  try {
    // TODO: Implement actual password reset logic
    await new Promise(resolve => setTimeout(resolve, 1500)); // Simulate API call
    
    // Show success state
    emailSent.value = true;
  } catch (error) {
    console.error('Password reset failed:', error);
    // TODO: Show error message
  } finally {
    isLoading.value = false;
  }
};

// Reset form to send another email
const resetForm = () => {
  emailSent.value = false;
  email.value = '';
};
</script>

<style scoped>
.forgot-password-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  padding: 2.5rem 2rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 460px;
  width: 100%;
}

/* Logo Section */
.logo-section {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  width: 80px;
  height: auto;
  margin-bottom: 1rem;
}

.logo-section h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #0a1f1a;
  margin-bottom: 0.5rem;
}

.logo-section p {
  color: #666;
  font-size: 0.95rem;
  line-height: 1.5;
}

.logo-section .success-text {
  color: #558b2f;
  font-weight: 600;
}

/* Form */
.reset-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #333;
}

.form-group input {
  padding: 0.875rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: white;
}

.form-group input:focus {
  outline: none;
  border-color: #558b2f;
  box-shadow: 0 0 0 3px rgba(85, 139, 47, 0.1);
}

.form-group input::placeholder {
  color: #999;
}

/* Info Box */
.info-box {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 1rem;
  background: rgba(85, 139, 47, 0.05);
  border: 1px solid rgba(85, 139, 47, 0.2);
  border-radius: 8px;
  font-size: 0.875rem;
  color: #333;
  line-height: 1.5;
}

.info-box svg {
  color: #558b2f;
  flex-shrink: 0;
  margin-top: 0.1rem;
}

/* Success State */
.success-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  text-align: center;
}

.success-icon {
  animation: scaleIn 0.4s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.success-message {
  font-size: 1rem;
  color: #333;
  line-height: 1.6;
}

.success-message strong {
  color: #558b2f;
  font-weight: 600;
}

.success-info {
  font-size: 0.9rem;
  color: #666;
  line-height: 1.6;
  max-width: 90%;
}

/* Buttons */
.submit-btn,
.secondary-btn {
  width: 100%;
  padding: 1rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0.5rem;
}

.submit-btn {
  background: #558b2f;
  color: white;
}

.submit-btn:hover:not(:disabled) {
  background: #3d6121;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(85, 139, 47, 0.3);
}

.submit-btn:active:not(:disabled) {
  transform: translateY(0);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.secondary-btn {
  background: transparent;
  color: #558b2f;
  border: 2px solid #558b2f;
}

.secondary-btn:hover {
  background: rgba(85, 139, 47, 0.05);
  transform: translateY(-1px);
}

.secondary-btn:active {
  transform: translateY(0);
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Login Link */
.login-link {
  text-align: center;
  font-size: 0.9rem;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
  margin-top: 0.5rem;
}

.login-link a {
  color: #558b2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.login-link a:hover {
  color: #3d6121;
  text-decoration: underline;
}

.login-link svg {
  transition: transform 0.2s ease;
}

.login-link a:hover svg {
  transform: translateX(-2px);
}

/* Responsive */
@media (max-width: 480px) {
  .forgot-password-card {
    padding: 2rem 1.5rem;
  }

  .logo-section h1 {
    font-size: 1.5rem;
  }

  .success-info {
    max-width: 100%;
  }
}
</style>