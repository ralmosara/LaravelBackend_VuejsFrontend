<template>
  <div class="auth-container">
    <div class="auth-card card">
      <h1 class="text-center mb-4">Register</h1>

      <div v-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label class="form-label">Name</label>
          <input
            v-model="form.name"
            type="text"
            class="form-control"
            placeholder="Enter your name"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="form-control"
            placeholder="Enter your email"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="form-control"
            placeholder="Enter your password"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="form-control"
            placeholder="Confirm your password"
            required
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary"
          :disabled="authStore.loading"
          style="width: 100%;"
        >
          {{ authStore.loading ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <p class="text-center mt-3">
        Already have an account?
        <router-link to="/login">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const error = ref(null)

const handleRegister = async () => {
  try {
    error.value = null
    await authStore.register(form.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  }
}
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 2rem;
}

.auth-card {
  width: 100%;
  max-width: 450px;
}

h1 {
  color: var(--primary-color);
}

a {
  color: var(--primary-color);
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>
