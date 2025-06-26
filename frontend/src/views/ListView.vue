<script setup>
import { onMounted, ref } from 'vue';

const urls = ref([]);

onMounted(async () => {
  const res = await fetch('/api/urls');
  urls.value = await res.json();
});
</script>

<template>
  <div class="text-white max-w-xl mx-auto mt-20">
    <h2 class="text-2xl font-semibold mb-4">Alle Kurz-URLs</h2>
    <ul>
      <li
        v-for="url in urls"
        :key="url.shortCode"
        class="mb-2 flex gap-4"
      >
        <router-link
          :to="`/stats/${url.shortCode}`"
          class="underline"
        >
          {{ url.shortCode }}
        </router-link>
        <span
          class="text-gray-400 truncate max-w-60"
          :title="url.originalUrl"
        >
          {{ url.originalUrl }}
        </span>
        <span>Aufrufe: {{ url.clickCount }}</span>
      </li>
    </ul>
  </div>
</template>