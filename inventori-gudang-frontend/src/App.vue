<script setup>
import { useRouter } from 'vue-router'
import http from './api/http'
import { auth } from './stores/auth'

const router = useRouter()

async function logout() {
  try {
    await http.post('/logout')
  } catch {
    // Token sudah tidak berlaku di server; tetap keluar di sisi klien
  } finally {
    auth.clear()
    router.push({ name: 'login' })
  }
}
</script>

<template>
  <header v-if="auth.token" class="topbar">
    <div class="brand">Inventori Gudang</div>
    <div class="who">
      <span>{{ auth.user?.email }}</span>
      <button class="btn ghost" @click="logout">Keluar</button>
    </div>
  </header>
  <div v-if="auth.token" class="hazard"></div>
  <router-view />
</template>
