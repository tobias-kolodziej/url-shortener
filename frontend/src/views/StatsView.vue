<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const data = ref(null);

onMounted(async () => {
  const res = await fetch(`/api/urls/${route.params.code}`);

  if (res.status === 404) {
    router.push('/not-found');
  } else {
    data.value = await res.json();
  }
});
</script>

<template>
  <div
    v-if="data"
    class="text-white max-w-xl mx-auto mt-20"
  >
    <h2 class="text-2xl font-semibold mb-4">Statistiken</h2>
    <div class="grid grid-cols-2">
      <strong>Kurzcode:</strong>
      <span>{{ data.shortCode }}</span>
      
      <strong>Original-URL:</strong>
      <a
        :href="data.originalUrl"
        target="_blank"
        class="underline truncate inline-block max-w-60"
        :title="data.originalUrl"
      >
        {{ data.originalUrl }}
      </a>
      
      <strong>Klicks:</strong>
      <span>{{ data.clickCount }}</span>

      <strong>Erstellt am:</strong>
      <span>{{ new Date(data.createdAt).toLocaleString() }}</span>
    </div>
  </div>
</template>
