import axios from 'axios'
import { auth } from '../stores/auth'
import router from '../router'

// Alamat API diambil dari variabel lingkungan (.env), bukan ditulis di komponen
const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: { Accept: 'application/json' },
})

// Kirim token di setiap permintaan
http.interceptors.request.use((config) => {
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

// Token ditolak/kedaluwarsa -> kembali ke halaman login
http.interceptors.response.use(
  (response) => response,
  (error) => {
    const isLoginRequest = error.config?.url === '/login'
    if (error.response?.status === 401 && !isLoginRequest) {
      auth.clear()
      router.push({ name: 'login' })
    }
    return Promise.reject(error)
  },
)

export default http
