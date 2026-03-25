<!-- src/components/auth/LoginForm.vue -->
<template>
  <div class="login-card">
    <!-- Logo -->
    <div class="logo-section">
      <img :src="logo" alt="Techne-Fixer" class="logo" />
      <h1>Welcome Back</h1>
      <p>Sign in to your account</p>
    </div>

    <!-- Login Form -->
    <form @submit.prevent="handleLogin" class="login-form">
      <!-- Email Input -->
      <div class="form-group">
        <label for="email">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="your.email@example.com"
          required
        />
      </div>

      <!-- Password Input -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-input">
          <input
            id="password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Enter your password"
            required
          />
          <button
            type="button"
            class="toggle-password"
            @click="showPassword = !showPassword"
            tabindex="-1"
          >
            <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M1 10s3-7 9-7 9 7 9 7-3 7-9 7-9-7-9-7z" stroke="currentColor" stroke-width="2"/>
              <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
            <svg v-else width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M14.12 14.12A7 7 0 0 1 3 10M9.88 5.88A7 7 0 0 1 17 10M1 1l18 18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Remember Me & Forgot Password -->
      <div class="form-options">
        <label class="checkbox-label">
          <input type="checkbox" v-model="rememberMe" />
          <span>Remember me</span>
        </label>
        <router-link to="/forgot-password" class="forgot-link">Forgot password?</router-link>
      </div>

      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
      
      <!-- Submit Button -->
      <button type="submit" class="submit-btn" :disabled="isLoading">
        <span v-if="!isLoading">Sign In</span>
        <span v-else class="loading">
          <span class="spinner"></span>
          Signing in...
        </span>
      </button>

      <!-- Sign Up Link -->
      <div class="signup-link">
        Don't have an account? <router-link to="/auth/register">Sign up</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import logo from '@/assets/images/logo.png';

const router = useRouter();
const auth = useAuthStore();

// Form state
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

// Handle login
const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try{
    await auth.login({email: email.value, password: password.value});
    router.push('/admin/dashboard');
  }catch(error){
    errorMessage.value = error.response?.data?.message || 'Invalid Credentials. Please try again.';
  }finally{
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  padding: 2.5rem 2rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
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
  margin-bottom: 0.25rem;
}

.logo-section p {
  color: #666;
  font-size: 0.95rem;
}

/* Form */
.login-form {
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

/* Password Input */
.password-input {
  position: relative;
}

.password-input input {
  width: 100%;
  padding-right: 3rem;
}

.toggle-password {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.toggle-password:hover {
  color: #558b2f;
}

/* Form Options */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: #333;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #558b2f;
}

.forgot-link {
  color: #558b2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #3d6121;
  text-decoration: underline;
}

/* Submit Button */
.submit-btn {
  width: 100%;
  padding: 1rem;
  background: #558b2f;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0.5rem;
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

.error-message {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
  text-align: center;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Sign Up Link */
.signup-link {
  text-align: center;
  font-size: 0.9rem;
  color: #666;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.signup-link a {
  color: #558b2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.signup-link a:hover {
  color: #3d6121;
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
  }

  .logo-section h1 {
    font-size: 1.5rem;
  }

  .form-options {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }
}
</style>