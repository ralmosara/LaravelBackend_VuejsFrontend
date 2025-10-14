<template>
  <div class="auth-container">
    <!-- Left Side - Illustration -->
    <div class="auth-illustration">
      <div class="illustration-content">
        <div class="brand-section">
          <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
            </svg>
          </div>
          <h1 class="brand-name">EduWay</h1>
        </div>

        <div class="illustration-text">
          <h2>Welcome Back!</h2>
          <p>Access your dashboard and manage your business efficiently with our powerful admin tools.</p>
        </div>

        <div class="features-list">
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>Secure Authentication</span>
          </div>
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>Real-time Analytics</span>
          </div>
          <div class="feature-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>Advanced Management</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="auth-form-section">
      <div class="auth-card">
        <div class="auth-header">
          <h1>Sign In</h1>
          <p>Enter your credentials to continue</p>
        </div>

        <div v-if="error" class="alert alert-error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ error }}
        </div>

        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label class="form-label">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              Email Address
            </label>
            <input
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="name@example.com"
              required
              autocomplete="email"
            />
          </div>

          <div class="form-group">
            <div class="label-with-link">
              <label class="form-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Password
              </label>
              <a href="#" class="forgot-link">Forgot?</a>
            </div>
            <div class="password-input-wrapper">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
              />
              <button
                type="button"
                class="password-toggle"
                @click="showPassword = !showPassword"
                tabindex="-1"
              >
                <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox" v-model="rememberMe" />
              <span class="checkbox-custom"></span>
              <span class="checkbox-text">Remember me</span>
            </label>
          </div>

          <button
            type="submit"
            class="btn btn-primary btn-auth"
            :disabled="authStore.loading"
          >
            <span v-if="!authStore.loading">
              Sign In
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </span>
            <span v-else class="loading-text">
              <svg class="spinner-icon" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                <path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round"/>
              </svg>
              Signing in...
            </span>
          </button>
        </form>

        <div class="auth-footer">
          <p>
            Don't have an account?
            <router-link to="/register" class="auth-link">Create Account</router-link>
          </p>
        </div>
      </div>

      <div class="auth-bottom-info">
        <p>&copy; 2024 EduWay. All rights reserved.</p>
      </div>
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
  email: '',
  password: '',
})

const error = ref(null)
const showPassword = ref(false)
const rememberMe = ref(false)

const handleLogin = async () => {
  try {
    error.value = null
    await authStore.login(form.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please try again.'
  }
}
</script>

<style scoped>
.auth-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
  background: var(--bg-secondary);
}

/* Left Side - Illustration */
.auth-illustration {
  background: var(--primary-gradient);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  position: relative;
  overflow: hidden;
}

.auth-illustration::before {
  content: '';
  position: absolute;
  width: 500px;
  height: 500px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  top: -250px;
  right: -250px;
}

.auth-illustration::after {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  bottom: -150px;
  left: -150px;
}

.illustration-content {
  position: relative;
  z-index: 1;
  color: white;
  max-width: 500px;
  animation: fadeIn 0.6s ease-out;
}

.brand-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 3rem;
}

.brand-icon {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.brand-icon svg {
  width: 32px;
  height: 32px;
}

.brand-name {
  font-size: 2rem;
  font-weight: 700;
  color: white;
  margin: 0;
}

.illustration-text {
  margin-bottom: 3rem;
}

.illustration-text h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 1rem 0;
  line-height: 1.2;
}

.illustration-text p {
  font-size: 1.125rem;
  opacity: 0.9;
  line-height: 1.6;
  margin: 0;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1rem;
  opacity: 0.95;
}

.feature-item svg {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

/* Right Side - Form */
.auth-form-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  background: white;
}

.auth-card {
  width: 100%;
  max-width: 460px;
  animation: slideIn 0.6s ease-out;
}

.auth-header {
  margin-bottom: 1.5rem;
}

.auth-header h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.375rem 0;
  letter-spacing: -0.01em;
}

.auth-header p {
  font-size: 0.9375rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Alert */
.alert {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.alert svg {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Form */
.auth-form {
  margin-bottom: 2rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.625rem;
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--text-primary);
}

.form-label svg {
  width: 16px;
  height: 16px;
  color: var(--text-tertiary);
}

.label-with-link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.625rem;
}

.forgot-link {
  font-size: 0.875rem;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--transition-base);
}

.forgot-link:hover {
  color: var(--primary-dark);
}

/* Password Input */
.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  color: var(--text-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-md);
  transition: all var(--transition-base);
}

.password-toggle:hover {
  color: var(--primary-color);
  background: var(--bg-secondary);
}

.password-toggle svg {
  width: 20px;
  height: 20px;
}

/* Form Options */
.form-options {
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  cursor: pointer;
  user-select: none;
}

.checkbox-label input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid var(--border-medium);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-base);
  background: white;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
  background: var(--primary-color);
  border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
  content: '';
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-text {
  font-size: 0.9375rem;
  color: var(--text-secondary);
}

/* Auth Button */
.btn-auth {
  width: 100%;
  padding: 0.875rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  justify-content: center;
}

.btn-auth span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-auth svg {
  width: 20px;
  height: 20px;
}

.loading-text {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner-icon {
  width: 20px;
  height: 20px;
  animation: spin 0.8s linear infinite;
}

/* Auth Footer */
.auth-footer {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid var(--border-medium);
}

.auth-footer p {
  font-size: 0.9375rem;
  color: var(--text-secondary);
  margin: 0;
}

.auth-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--transition-base);
}

.auth-link:hover {
  color: var(--primary-dark);
  text-decoration: underline;
}

.auth-bottom-info {
  margin-top: 2rem;
  text-align: center;
}

.auth-bottom-info p {
  font-size: 0.875rem;
  color: var(--text-tertiary);
  margin: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .auth-container {
    grid-template-columns: 1fr;
  }

  .auth-illustration {
    display: none;
  }

  .auth-form-section {
    padding: 2rem;
  }
}

@media (max-width: 640px) {
  .auth-form-section {
    padding: 1.5rem;
  }

  .auth-header h1 {
    font-size: 1.75rem;
  }
}
</style>
