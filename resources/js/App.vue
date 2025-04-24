<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar bg-light p-3" style="min-width: 220px; min-height: 100vh;">
      <h4 class="mb-4">Dashboard</h4>
      <ul class="nav flex-column">
        <li class="nav-item mb-2">
          <router-link class="nav-link" to="/products">Products</router-link>
        </li>
        <!-- Add more sidebar links here as needed -->
      </ul>
    </nav>
    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
      <router-view />
    </div>
  </div>
</template>

<script setup>
// No logic needed for the root component
</script>

<style scoped>
.sidebar {
  border-right: 1px solid #dee2e6;
  height: 100vh;
}
</style>