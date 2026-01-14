import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('@/views/Products.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: () => import('@/views/Users.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/schedule',
    name: 'Schedule',
    component: () => import('@/views/Schedule.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: () => import('@/views/Tasks.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: () => import('@/views/Reports.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/chat',
    name: 'Chat',
    component: () => import('@/views/Chat.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notes',
    name: 'Notes',
    component: () => import('@/views/Notes.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('@/views/Settings.vue'),
    meta: { requiresAuth: true }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Check authentication first
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
    return
  }

  // For authenticated routes, ensure user data is loaded
  if (to.meta.requiresAuth && authStore.isAuthenticated && !authStore.user) {
    await authStore.checkAuth()
  }

  // Check admin access
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    alert('Access denied. Admin privileges required.')
    next('/')
    return
  }

  // Guest routes redirect to home if authenticated
  if (to.meta.guest && authStore.isAuthenticated) {
    next('/')
    return
  }

  next()
})

export default router
