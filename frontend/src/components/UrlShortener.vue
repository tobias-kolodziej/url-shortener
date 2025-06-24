<script setup lang="ts">
import { ref } from 'vue'

const url = ref('')
const shortUrl = ref('')
const copied = ref(false)

async function shorten() {
  if (!url.value) return

  // try {
  //   const response = await fetch('/shorten', {
  //     method: 'POST',
  //     headers: { 'Content-Type': 'application/json' },
  //     body: JSON.stringify({ url: url.value })
  //   })
  //   const data = await response.json()
  //   shortUrl.value = data.shortUrl
  // } catch (err) {
  //   console.error('Fehler beim Kürzen:', err)
  // }
  shortUrl.value = "https://short.url"
  copied.value = false
}

function copyToClipboard() {
  navigator.clipboard.writeText(shortUrl.value).then(() => {
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  })
}
</script>

<template>
  <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-md space-y-4">
    <h1 class="text-2xl font-semibold text-gray-800">Kurz-URL erstellen</h1>

    <input
      v-model="url"
      type="url"
      placeholder="https://example.com"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
    />

    <button
      @click="shorten"
      class="w-full bg-gray-900 text-white py-2 px-4 rounded-lg hover:bg-gray-800 transition"
    >
      Kürzen
    </button>

    <div v-if="shortUrl" class="text-center space-y-2">
      <p class="text-gray-600">Deine Kurz-URL:</p>
      <a
        :href="shortUrl"
        class="text-blue-600 underline break-all block"
        target="_blank"
      >
        {{ shortUrl }}
      </a>
      <button
        @click="copyToClipboard"
        class="mt-1 inline-block text-sm text-white bg-blue-600 px-3 py-1 rounded hover:bg-blue-500 transition"
      >
        {{ copied ? 'Kopiert!' : 'In Zwischenablage' }}
      </button>
    </div>
  </div>
</template>
