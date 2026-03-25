<!-- src/views/Manager/Dashboard.vue -->
<template>
  <div class="dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin panel!</p>
    
    <!-- Add your dashboard content here -->
    <div class="dashboard-grid">
      <div class="card">
        <h3>Total Users</h3>
        <p class="stat">0</p>
      </div>
      <div class="card">
        <h3>Total Orders</h3>
        <p class="stat">0</p>
      </div>
      <div class="card">
        <h3>Revenue</h3>
        <p class="stat">$0</p>
      </div>
    </div>
  </div>
</template>

<script setup>
// Your dashboard logic will go here
</script>

<style scoped>
.dashboard {
  width: 100%;
}

.dashboard h1 {
  margin-bottom: 2rem;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card h3 {
  margin-bottom: 1rem;
  color: #2c3e50;
}

.stat {
  font-size: 2rem;
  font-weight: bold;
  color: #3498db;
}
</style>