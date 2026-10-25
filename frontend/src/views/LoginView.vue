<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../api/http'
import { auth } from '../stores/auth'

const router = useRouter()
const form = ref({ email: '', password: '' })
const errors = ref({})
const message = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  errors.value = {}
  message.value = ''
  try {
    const { data } = await http.post('/login', form.value)
    auth.set(data.token, data.user) 
    router.push({ name: 'barang' })
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors
    } else if (e.response) {
      message.value = e.response.data.message || 'Login gagal.'
    } else {
      message.value = 'API tidak dapat dihubungi. Periksa VITE_API_BASE_URL di file .env dan pastikan server Laravel berjalan.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="login">
    <aside class="login-side">
      <div class="hazard tall"></div>
      <h1>Inventori<br />Gudang</h1>
      <p>Catat stok masuk, cek lokasi rak, dan rapikan data barang dari satu tempat.</p>
    </aside>

    <section class="login-form">
      <form @submit.prevent="submit" novalidate>
        <h2>Masuk</h2>

        <p v-if="message" class="alert" role="alert">{{ message }}</p>

        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" autocomplete="username" required />
        <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>

        <label for="password">Password</label>
        <input id="password" v-model="form.password" type="password" autocomplete="current-password" required />
        <p v-if="errors.password" class="err">{{ errors.password[0] }}</p>

        <button class="btn primary wide" :disabled="loading">
          {{ loading ? 'Memeriksa...' : 'Masuk' }}
        </button>
      </form>
    </section>
  </main>
</template>
