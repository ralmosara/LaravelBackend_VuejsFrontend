<template>
  <div class="container">
    <div class="users-page">
      <div class="page-header flex justify-between align-center mb-4">
        <h1>Users</h1>
        <div class="flex gap-2">
          <button @click="showStatistics" class="btn btn-secondary">
            Statistics
          </button>
          <button @click="showModal = true" class="btn btn-primary">
            Add User
          </button>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="tabs mb-3">
        <button
          @click="filterUsers('all')"
          :class="['tab', { active: activeFilter === 'all' }]"
        >
          All Users
        </button>
        <button
          @click="filterUsers('verified')"
          :class="['tab', { active: activeFilter === 'verified' }]"
        >
          Verified
        </button>
        <button
          @click="filterUsers('unverified')"
          :class="['tab', { active: activeFilter === 'unverified' }]"
        >
          Unverified
        </button>
        <button
          @click="filterUsers('admins')"
          :class="['tab', { active: activeFilter === 'admins' }]"
        >
          Admins
        </button>
      </div>

      <div v-if="userStore.loading" class="loading">
        <div class="spinner"></div>
      </div>

      <div v-else-if="userStore.users.length === 0" class="card text-center">
        <p style="padding: 2rem; color: var(--text-light);">
          No users found. Click "Add User" to create one.
        </p>
      </div>

      <div v-else class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in userStore.users" :key="user.id">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <span :class="['badge', user.role === 'admin' ? 'badge-primary' : 'badge-secondary']">
                  {{ user.role || 'user' }}
                </span>
              </td>
              <td>
                <span :class="['badge', user.is_verified ? 'badge-success' : 'badge-warning']">
                  {{ user.is_verified ? 'Verified' : 'Unverified' }}
                </span>
              </td>
              <td>{{ user.created_at_human || user.created_at }}</td>
              <td>
                <div class="flex gap-2">
                  <button @click="editUser(user)" class="btn btn-sm btn-primary">
                    Edit
                  </button>
                  <button @click="showRoleModal(user)" class="btn btn-sm btn-secondary">
                    Role
                  </button>
                  <button @click="deleteUserHandler(user.id)" class="btn btn-sm btn-danger">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit User Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal-content card" @click.stop>
          <h2 class="mb-3">{{ editMode ? 'Edit User' : 'Add User' }}</h2>

          <div v-if="error" class="alert alert-error">
            {{ error }}
          </div>

          <form @submit.prevent="saveUser">
            <div class="form-group">
              <label class="form-label">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="form-control"
                placeholder="Full name"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="form-control"
                placeholder="email@example.com"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Password {{ editMode ? '(leave blank to keep current)' : '' }}</label>
              <input
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="Password"
                :required="!editMode"
                minlength="8"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Confirm password"
                :required="!editMode && form.password"
              />
            </div>

            <div class="flex gap-2 justify-between">
              <button type="button" @click="closeModal" class="btn">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary" :disabled="userStore.loading">
                {{ userStore.loading ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Statistics Modal -->
      <div v-if="showStatsModal" class="modal-overlay" @click="showStatsModal = false">
        <div class="modal-content card" @click.stop>
          <h2 class="mb-3">User Statistics</h2>

          <div v-if="userStore.statistics" class="stats-grid">
            <div class="stat-card">
              <div class="stat-value">{{ userStore.statistics.total_users }}</div>
              <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ userStore.statistics.verified_users }}</div>
              <div class="stat-label">Verified Users</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ userStore.statistics.unverified_users }}</div>
              <div class="stat-label">Unverified Users</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ userStore.statistics.recent_users }}</div>
              <div class="stat-label">Recent Users (7 days)</div>
            </div>
          </div>

          <div class="flex justify-end mt-3">
            <button @click="showStatsModal = false" class="btn">
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Update Role Modal -->
      <div v-if="showRoleUpdateModal" class="modal-overlay" @click="closeRoleModal">
        <div class="modal-content card" @click.stop>
          <h2 class="mb-3">Update User Role</h2>

          <div v-if="roleError" class="alert alert-error">
            {{ roleError }}
          </div>

          <div v-if="selectedUser" class="mb-3">
            <p><strong>User:</strong> {{ selectedUser.name }}</p>
            <p><strong>Email:</strong> {{ selectedUser.email }}</p>
            <p><strong>Current Role:</strong>
              <span :class="['badge', selectedUser.role === 'admin' ? 'badge-primary' : 'badge-secondary']">
                {{ selectedUser.role || 'user' }}
              </span>
            </p>
          </div>

          <form @submit.prevent="updateRole">
            <div class="form-group">
              <label class="form-label">New Role</label>
              <select v-model="newRole" class="form-control" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="flex gap-2 justify-between">
              <button type="button" @click="closeRoleModal" class="btn">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary" :disabled="userStore.loading">
                {{ userStore.loading ? 'Updating...' : 'Update Role' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/users'

const userStore = useUserStore()
const showModal = ref(false)
const showStatsModal = ref(false)
const showRoleUpdateModal = ref(false)
const editMode = ref(false)
const error = ref(null)
const roleError = ref(null)
const activeFilter = ref('all')
const selectedUser = ref(null)
const newRole = ref('user')

const form = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

onMounted(() => {
  userStore.fetchUsers()
})

const filterUsers = async (filter) => {
  activeFilter.value = filter

  try {
    if (filter === 'verified') {
      await userStore.fetchVerifiedUsers()
    } else if (filter === 'unverified') {
      await userStore.fetchUnverifiedUsers()
    } else if (filter === 'admins') {
      await userStore.fetchAdminUsers()
    } else {
      await userStore.fetchUsers()
    }
  } catch (err) {
    console.error('Failed to filter users:', err)
  }
}

const editUser = (user) => {
  editMode.value = true
  form.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editMode.value = false
  error.value = null
  form.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  }
}

const saveUser = async () => {
  try {
    error.value = null

    // Validate password confirmation
    if (form.value.password && form.value.password !== form.value.password_confirmation) {
      error.value = 'Password confirmation does not match'
      return
    }

    const userData = {
      name: form.value.name,
      email: form.value.email,
    }

    // Only include password if provided
    if (form.value.password) {
      userData.password = form.value.password
      userData.password_confirmation = form.value.password_confirmation
    }

    if (editMode.value) {
      await userStore.updateUser(form.value.id, userData)
    } else {
      await userStore.createUser(userData)
    }

    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Failed to save user'
  }
}

const deleteUserHandler = async (id) => {
  if (!confirm('Are you sure you want to delete this user?')) return

  try {
    await userStore.deleteUser(id)
  } catch (err) {
    alert('Failed to delete user')
  }
}

const showStatistics = async () => {
  try {
    await userStore.fetchStatistics()
    showStatsModal.value = true
  } catch (err) {
    alert('Failed to load statistics')
  }
}

const showRoleModal = (user) => {
  selectedUser.value = user
  newRole.value = user.role || 'user'
  roleError.value = null
  showRoleUpdateModal.value = true
}

const closeRoleModal = () => {
  showRoleUpdateModal.value = false
  selectedUser.value = null
  newRole.value = 'user'
  roleError.value = null
}

const updateRole = async () => {
  try {
    roleError.value = null
    await userStore.updateUserRole(selectedUser.value.id, newRole.value)
    closeRoleModal()
    // Refresh the list
    await filterUsers(activeFilter.value)
  } catch (err) {
    roleError.value = err.response?.data?.message || 'Failed to update role'
  }
}
</script>

<style scoped>
.users-page {
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  color: var(--text-color);
  font-size: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.table-container {
  overflow-x: auto;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 0.5rem;
  border-bottom: 2px solid var(--border-color);
  padding-bottom: 0;
}

.tab {
  padding: 0.75rem 1.5rem;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: var(--text-light);
  cursor: pointer;
  transition: all 0.3s;
  margin-bottom: -2px;
  font-size: 0.95rem;
}

.tab:hover {
  color: var(--primary-color);
}

.tab.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
}

/* Badge */
.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.badge-warning {
  background: rgba(251, 191, 36, 0.1);
  color: #fbbf24;
}

.badge-primary {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.badge-secondary {
  background: rgba(107, 114, 128, 0.1);
  color: #6b7280;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  margin: 1rem;
}

.modal-content h2 {
  color: var(--text-color);
}

/* Statistics */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  margin: 1.5rem 0;
}

.stat-card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-light);
}

.btn-secondary {
  background: var(--secondary-color, #6b7280);
}

.btn-secondary:hover {
  background: var(--secondary-hover, #4b5563);
}
</style>
