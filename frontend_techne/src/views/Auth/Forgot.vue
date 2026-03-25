<!-- src/views/Auth/ForgotPassword.vue -->
<template>
  <div class="forgot-password-page">
    <!-- Simple Background -->
    <div class="forgot-password-background"></div>

    <!-- Forgot Password Container -->
    <div class="forgot-password-container">
      <ForgotPasswordForm />
    </div>
  </div>
</template>

<script setup>
import ForgotPasswordForm from '@/components/auth/PasswordResetForm.vue';
</script>

<style scoped>
.forgot-password-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6rem 1rem 2rem; /* Top padding accounts for fixed navbar */
  position: relative;
}

/* Simple Background */
.forgot-password-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #0f2d24 0%, #0a1f1a 100%);
  z-index: -1;
}

/* Forgot Password Container */
.forgot-password-container {
  width: 100%;
  max-width: 460px;
}

/* Responsive */
@media (max-width: 480px) {
  .forgot-password-page {
    padding: 5rem 1rem 2rem; /* Less top padding on mobile */
  }
}
</style>