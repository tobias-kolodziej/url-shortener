<script setup>
    import Navigation from './components/Navigation.vue'
</script>

<template>
  <main class="min-h-screen bg-gray-900 p-4">
    <Navigation />
    <router-view />
  </main>
</template>
