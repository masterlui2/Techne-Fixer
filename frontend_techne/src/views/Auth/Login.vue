<!-- src/views/Auth/Login.vue -->
<template>
  <div class="login-page">
    <!-- Simple Background -->
    <div class="login-background"></div>

    <!-- Login Container -->
    <div class="login-container">
      <LoginForm />
    </div>
  </div>
</template>

<script setup>
import LoginForm from '@/components/auth/LoginForm.vue';
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6rem 1rem 2rem; /* Top padding accounts for fixed navbar */
  position: relative;
}

/* Simple Background */
.login-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #0f2d24 0%, #0a1f1a 100%);
  z-index: -1;
}

/* Login Container */
.login-container {
  width: 100%;
  max-width: 420px;
}

/* Responsive */
@media (max-width: 480px) {
  .login-page {
    padding: 5rem 1rem 2rem; /* Less top padding on mobile */
  }
}
</style>