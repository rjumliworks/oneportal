<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-semibold mb-3 text-center">📄 Document Verification</h2>
      <p class="text-gray-500 text-sm mb-6 text-center">
        Upload your signed PDF to verify its authenticity and integrity.
      </p>

      <input
        type="file"
        accept="application/pdf"
        @change="onFileChange"
        class="w-full border border-gray-300 rounded-lg p-2 mb-4"
      />

      <button
        @click="verifyDocument"
        :disabled="loading || !file"
        class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg w-full disabled:opacity-50"
      >
        {{ loading ? 'Verifying...' : 'Verify Document' }}
      </button>

      <div v-if="result" class="mt-6 text-center">
        <div
          :class="{
            'text-green-600': result.status === 'valid',
            'text-red-600': result.status === 'tampered',
            'text-yellow-600': result.status === 'missing'
          }"
          class="text-lg font-medium"
        >
          {{ result.message }}
        </div>
      </div>

      <div v-if="error" class="text-red-500 mt-3 text-center text-sm">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      file: null,
      result: null,
      error: null,
      loading: false,
    }
  },

  methods: {
    onFileChange(e) {
      this.file = e.target.files[0]
      this.result = null
      this.error = null
    },

    async verifyDocument() {
      if (!this.file) return

      this.loading = true
      this.result = null
      this.error = null

      const formData = new FormData()
      formData.append('file', this.file)

      try {
        const response = await axios.post('/verify', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        this.result = response.data
      } catch (err) {
        if (err.response && err.response.data) {
          this.result = err.response.data
        } else {
          this.error = 'Verification failed. Please try again.'
        }
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.card {
  transition: all 0.3s ease;
}
</style>
