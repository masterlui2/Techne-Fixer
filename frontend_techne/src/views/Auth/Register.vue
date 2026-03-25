<!-- src/views/Auth/Register.vue -->
<template>
  <div class="register-page">
    <!-- Simple Background -->
    <div class="register-background"></div>

    <!-- Register Container -->
    <div class="register-container">
      <RegisterForm />
    </div>
  </div>
</template>

<script setup>
import RegisterForm from '@/components/auth/RegisterForm.vue';
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6rem 1rem 2rem; /* Top padding accounts for fixed navbar */
  position: relative;
}

/* Simple Background */
.register-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #0f2d24 0%, #0a1f1a 100%);
  z-index: -1;
}

/* Register Container */
.register-container {
  width: 100%;
  max-width: 480px;
}

/* Responsive */
@media (max-width: 480px) {
  .register-page {
    padding: 5rem 1rem 2rem; /* Less top padding on mobile */
  }
}
</style>