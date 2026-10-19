import { createRouter, createWebHistory } from 'vue-router'
import { auth } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
    { path: '/', name: 'barang', component: () => import('../views/BarangView.vue'), meta: { auth: true } },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to) => {
  if (to.meta.auth && !auth.token) return { name: 'login' }
  if (to.name === 'login' && auth.token) return { name: 'barang' }
})

export default router
