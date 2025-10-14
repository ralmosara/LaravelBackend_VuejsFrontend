<template>
  <nav class="navbar">
    <div class="navbar-content">
      <!-- Burger Menu -->
      <button @click="toggleSidebar" class="burger-menu" aria-label="Toggle menu">
        <span class="burger-line"></span>
        <span class="burger-line"></span>
        <span class="burger-line"></span>
      </button>

      <!-- Search Bar -->
      <div class="search-container">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="search" placeholder="Search anything..." class="search-input" />
        <kbd class="search-kbd">Ctrl K</kbd>
      </div>

      <!-- Right Section -->
      <div class="navbar-actions">
        <!-- Notifications -->
        <button class="icon-btn" title="Notifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 01-3.46 0"/>
          </svg>
          <span class="badge-dot"></span>
        </button>

        <!-- Settings -->
        <button class="icon-btn" title="Settings">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
          </svg>
        </button>

        <!-- User Profile -->
        <div class="user-menu">
          <div class="user-info">
            <div class="user-avatar">
              <img :src="`https://ui-avatars.com/api/?name=${authStore.user?.name}&background=1e40af&color=fff`" :alt="authStore.user?.name" />
              <span class="status-indicator"></span>
            </div>
            <div class="user-details">
              <span class="user-name">{{ authStore.user?.name }}</span>
              <span class="user-role">{{ authStore.user?.role || 'User' }}</span>
            </div>
          </div>
          <button @click="handleLogout" class="logout-btn" title="Logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSidebar } from '@/composables/useSidebar'

const router = useRouter()
const authStore = useAuthStore()
const { toggleSidebar } = useSidebar()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  background: white;
  border-bottom: 1px solid var(--border-medium);
  padding: 0.875rem 2rem;
  position: sticky;
  top: 0;
  z-index: 50;
  backdrop-filter: blur(8px);
  background-color: rgba(255, 255, 255, 0.95);
}

.navbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
}

/* Burger Menu */
.burger-menu {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: var(--radius-md);
  transition: all var(--transition-base);
}

.burger-menu:hover {
  background: var(--bg-secondary);
}

.burger-line {
  width: 24px;
  height: 3px;
  background: var(--text-primary);
  border-radius: 2px;
  transition: all var(--transition-base);
}

.burger-menu:hover .burger-line {
  background: var(--primary-color);
}

/* Search Bar */
.search-container {
  position: relative;
  flex: 1;
  max-width: 500px;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: var(--text-tertiary);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 2px solid var(--border-medium);
  border-radius: var(--radius-lg);
  font-size: 0.9375rem;
  color: var(--text-primary);
  background-color: var(--bg-secondary);
  transition: all var(--transition-base);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  background-color: white;
  box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
}

.search-input::placeholder {
  color: var(--text-tertiary);
}

.search-kbd {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  padding: 0.25rem 0.5rem;
  background: var(--bg-tertiary);
  border: 1px solid var(--border-medium);
  border-radius: var(--radius-sm);
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-tertiary);
  pointer-events: none;
}

/* Actions */
.navbar-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.icon-btn {
  position: relative;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  border-radius: var(--radius-lg);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all var(--transition-base);
}

.icon-btn:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.icon-btn svg {
  width: 20px;
  height: 20px;
}

.badge-dot {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: var(--danger-color);
  border: 2px solid white;
  border-radius: 50%;
}

/* User Menu */
.user-menu {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: var(--radius-lg);
  background: var(--bg-secondary);
  transition: all var(--transition-base);
}

.user-menu:hover {
  background: var(--bg-tertiary);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid white;
  box-shadow: var(--shadow-sm);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.status-indicator {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 12px;
  height: 12px;
  background: var(--success-color);
  border: 2px solid white;
  border-radius: 50%;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
}

.user-role {
  font-size: 0.75rem;
  color: var(--text-tertiary);
  text-transform: capitalize;
  line-height: 1.2;
}

.logout-btn {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all var(--transition-base);
}

.logout-btn:hover {
  background: var(--danger-color);
  color: white;
}

.logout-btn svg {
  width: 18px;
  height: 18px;
}

@media (max-width: 1024px) {
  .burger-menu {
    display: flex;
  }
}

@media (max-width: 768px) {
  .search-container {
    max-width: 300px;
  }

  .user-details {
    display: none;
  }

  .icon-btn:first-of-type {
    display: none;
  }
}

@media (max-width: 640px) {
  .navbar {
    padding: 0.75rem 1rem;
  }

  .navbar-content {
    gap: 1rem;
  }

  .search-container {
    max-width: 200px;
  }

  .search-kbd {
    display: none;
  }

  .icon-btn {
    display: none;
  }

  .icon-btn:last-of-type {
    display: flex;
  }
}
</style>
