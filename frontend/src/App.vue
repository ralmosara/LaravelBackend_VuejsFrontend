<template>
  <div id="app">
    <template v-if="authStore.isAuthenticated">
      <!-- Overlay for mobile sidebar -->
      <div
        v-if="isSidebarOpen"
        class="sidebar-overlay"
        @click="closeSidebar"
      ></div>

      <Sidebar />
      <div class="main-wrapper">
        <Navbar />
        <main class="main-content">
          <router-view />
        </main>
      </div>
    </template>
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSidebar } from '@/composables/useSidebar'
import Navbar from '@/components/Navbar.vue'
import Sidebar from '@/components/Sidebar.vue'

const authStore = useAuthStore()
const { isSidebarOpen, closeSidebar } = useSidebar()

onMounted(() => {
  authStore.checkAuth()
})
</script>

<style scoped>
#app {
  display: flex;
  min-height: 100vh;
  background: #f8fafc;
  position: relative;
}

/* Sidebar Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  animation: fadeIn 0.3s ease-out;
  backdrop-filter: blur(2px);
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.main-wrapper {
  flex: 1;
  margin-left: 280px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.main-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

@media (max-width: 1024px) {
  .main-wrapper {
    margin-left: 0;
  }
}
</style>
