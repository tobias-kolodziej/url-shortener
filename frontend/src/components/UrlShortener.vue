<script setup lang="ts">
import { computed, ref } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, url as urlValidator } from '@vuelidate/validators'

const url = ref('')
const shortUrl = ref('')
const copied = ref(false)

const rules = computed(() => ({
  url: { required, url: urlValidator }
}))

const v$ = useVuelidate(rules, { url })

async function prependHttpsAndTouch() {
  if (!/^https?:\/\//i.test(url.value)) {
    url.value = 'https://' + url.value
  }

  v$.value.url.$touch()
}

async function shorten() {
  const result = await v$.value.$validate()
  if (!result) {
    console.log("invalid", url.value)
    console.log("valid?", v$.value.url.$errors)
    return
  }

  try {
    console.log("fetch")

    const response = await fetch('/api/urls', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url: url.value })
    })
    const data = await response.json()
    shortUrl.value = data.shortUrl
    copied.value = false
  } catch (err) {
    console.error('Fehler beim Kürzen:', err)
  }
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
      @blur="prependHttpsAndTouch"
      class="w-full px-4 py-2 rounded-lg focus:outline-none focus:ring transition"
      :class="{
        'border border-red-500 ring-red-300': v$.url.$error,
        'border border-gray-300 ring-blue-300': !v$.url.$error
      }"
    />
    <p
      v-if="v$.url.$error"
      class="text-sm text-red-600"
    >
      <span v-if="v$.url.$errors.find(e => e.$validator === 'required')">
        Bitte gib eine URL ein.
      </span>
      <span v-else>
        Bitte gib eine gültige URL ein.
      </span>
    </p>

    <button
      @click="shorten"
      class="w-full bg-gray-900 text-white py-2 px-4 rounded-lg hover:bg-gray-800 transition"
    >
      Kürzen
    </button>

    <div
      v-if="shortUrl"
      class="text-center space-y-2"
    >
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
