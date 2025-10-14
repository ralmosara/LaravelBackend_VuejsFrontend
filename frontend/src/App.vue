<template>
  <div id="app">
    <Navbar v-if="authStore.isAuthenticated" />
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Navbar from '@/components/Navbar.vue'

const authStore = useAuthStore()

onMounted(() => {
  authStore.checkAuth()
})
</script>

<style scoped>
main {
  min-height: calc(100vh - 70px);
  padding: 2rem 0;
}
</style>
