<!-- src/components/Auth/RegisterForm.vue -->
<template>
  <div class="register-card">
    <!-- Logo -->
    <div class="logo-section">
      <img :src="logo" alt="Techne-Fixer" class="logo" />
      <h1>Create Account</h1>
      <p>Sign up to get started</p>
    </div>

    <!-- Register Form -->
    <form @submit.prevent="handleRegister" class="register-form">
      <!-- Name Input -->
      <div class="form-group">
        <label for="name">Full Name</label>
        <input
          id="name"
          v-model="name"
          type="text"
          placeholder="John Doe"
          required
        />
      </div>

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
            placeholder="Create a strong password"
            required
            minlength="8"
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
        <div class="password-strength">
          <div class="strength-bar">
            <div class="strength-fill" :class="passwordStrength"></div>
          </div>
          <span class="strength-text">{{ passwordStrengthText }}</span>
        </div>
        <ul class="password-requirements">
          <li :class="{ met: password.length >= 8 }">
            <span class="req-icon">{{ password.length >= 8 ? '✓' : '✗' }}</span>
            Minimum 8 characters
          </li>
          <li :class="{ met: /\d/.test(password) }">
            <span class="req-icon">{{ /\d/.test(password) ? '✓' : '✗' }}</span>
            At least 1 number
          </li>
          <li :class="{ met: /[^a-zA-Z0-9]/.test(password) }">
            <span class="req-icon">{{ /[^a-zA-Z0-9]/.test(password) ? '✓' : '✗' }}</span>
            At least 1 special character
          </li>
        </ul>
      </div>

      <!-- Confirm Password Input -->
      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <div class="password-input">
          <input
            id="confirmPassword"
            v-model="confirmPassword"
            :type="showConfirmPassword ? 'text' : 'password'"
            placeholder="Re-enter your password"
            required
          />
          <button
            type="button"
            class="toggle-password"
            @click="showConfirmPassword = !showConfirmPassword"
            tabindex="-1"
          >
            <svg v-if="!showConfirmPassword" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M1 10s3-7 9-7 9 7 9 7-3 7-9 7-9-7-9-7z" stroke="currentColor" stroke-width="2"/>
              <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
            <svg v-else width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M14.12 14.12A7 7 0 0 1 3 10M9.88 5.88A7 7 0 0 1 17 10M1 1l18 18" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        <span v-if="confirmPassword && !passwordsMatch" class="error-text">
          Passwords do not match
        </span>
      </div>

      <!-- Terms & Conditions -->
      <label class="terms-label">
        <input type="checkbox" v-model="agreeToTerms" required />
        <span>
          I agree to the 
          <a href="#" class="link">Terms of Service</a> and 
          <a href="#" class="link">Privacy Policy</a>
        </span>
      </label>

      <!-- Submit Button -->
      <button 
        type="submit" 
        class="submit-btn" 
        :disabled="isLoading || !isFormValid"
      >
        <span v-if="!isLoading">Create Account</span>
        <span v-else class="loading">
          <span class="spinner"></span>
          Creating account...
        </span>
      </button>

      <!-- Login Link -->
      <div class="login-link">
        Already have an account? <router-link to="/login">Sign in</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import logo from '@/assets/images/logo.png';

const router = useRouter();
const auth = useAuthStore();

// Form state
const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const agreeToTerms = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

// Password requirement checks
const hasMinLength = computed(() => password.value.length >= 8);
const hasNumber = computed(() => /\d/.test(password.value));
const hasSpecialChar = computed(() => /[^a-zA-Z0-9]/.test(password.value));

// Password validation
const passwordStrength = computed(() => {
  const pwd = password.value;
  if (!pwd) return '';
  
  let strength = 0;
  if (pwd.length >= 8) strength++;
  if (pwd.length >= 12) strength++;
  if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
  if (/\d/.test(pwd)) strength++;
  if (/[^a-zA-Z0-9]/.test(pwd)) strength++;
  
  if (strength <= 2) return 'weak';
  if (strength <= 3) return 'medium';
  return 'strong';
});

const passwordStrengthText = computed(() => {
  if (!password.value) return '';
  const strength = passwordStrength.value;
  if (strength === 'weak') return 'Weak password';
  if (strength === 'medium') return 'Medium strength';
  return 'Strong password';
});

const passwordsMatch = computed(() => {
  if (!confirmPassword.value) return true;
  return password.value === confirmPassword.value;
});

const isFormValid = computed(() => {
  return name.value &&
         email.value &&
         hasMinLength.value &&
         hasNumber.value &&
         hasSpecialChar.value &&
         passwordsMatch.value &&
         agreeToTerms.value;
});

const handleRegister = async () => {
  if (!isFormValid.value) return;
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    });

    if (auth.isAdmin) {
      router.push('/admin/dashboard');
    } else {
      router.push('/'); // customers go to public site for now
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Registration failed. Please try again.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.register-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  padding: 2.5rem 2rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 480px;
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
  margin-bottom: 0.25rem;
}

.logo-section p {
  color: #666;
  font-size: 0.95rem;
}

/* Form */
.register-form {
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

/* Password Strength */
.password-strength {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

.strength-bar {
  flex: 1;
  height: 4px;
  background: #e0e0e0;
  border-radius: 2px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.strength-fill.weak {
  width: 33%;
  background: #ef5350;
}

.strength-fill.medium {
  width: 66%;
  background: #ff9800;
}

.strength-fill.strong {
  width: 100%;
  background: #558b2f;
}

.strength-text {
  font-size: 0.8rem;
  font-weight: 600;
  white-space: nowrap;
}

.strength-fill.weak + .strength-text {
  color: #ef5350;
}

.strength-fill.medium + .strength-text {
  color: #ff9800;
}

.strength-fill.strong + .strength-text {
  color: #558b2f;
}

/* Password Requirements */
.password-requirements {
  list-style: none;
  padding: 0;
  margin: 0.5rem 0 0;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.password-requirements li {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: #ef5350;
  transition: color 0.2s ease;
}

.password-requirements li.met {
  color: #558b2f;
}

.req-icon {
  font-size: 0.75rem;
  font-weight: 700;
  width: 14px;
  text-align: center;
}

/* Error Text */
.error-text {
  font-size: 0.85rem;
  color: #ef5350;
  margin-top: 0.25rem;
}

/* Terms & Conditions */
.terms-label {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #666;
  cursor: pointer;
  line-height: 1.5;
}

.terms-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #558b2f;
  margin-top: 0.1rem;
  flex-shrink: 0;
}

.terms-label .link {
  color: #558b2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.terms-label .link:hover {
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Login Link */
.login-link {
  text-align: center;
  font-size: 0.9rem;
  color: #666;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.login-link a {
  color: #558b2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.login-link a:hover {
  color: #3d6121;
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 480px) {
  .register-card {
    padding: 2rem 1.5rem;
  }

  .logo-section h1 {
    font-size: 1.5rem;
  }
}
</style>