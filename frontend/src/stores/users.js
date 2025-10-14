import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useUserStore = defineStore('users', () => {
  const users = ref([])
  const currentUser = ref(null)
  const statistics = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchUsers = async (params = {}) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/admin/users', { params })

      if (response.data.success) {
        users.value = response.data.data.data || response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async (id) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get(`/admin/users/${id}`)

      if (response.data.success) {
        currentUser.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.post('/admin/users', userData)

      if (response.data.success) {
        users.value.unshift(response.data.data)
        return response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (id, userData) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.put(`/admin/users/${id}`, userData)

      if (response.data.success) {
        const index = users.value.findIndex(u => u.id === id)
        if (index !== -1) {
          users.value[index] = response.data.data
        }
        return response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (id) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.delete(`/admin/users/${id}`)

      if (response.data.success) {
        users.value = users.value.filter(u => u.id !== id)
        return true
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchStatistics = async () => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/admin/users/statistics')

      if (response.data.success) {
        statistics.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch statistics'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchVerifiedUsers = async () => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/admin/users/verified')

      if (response.data.success) {
        users.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch verified users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchUnverifiedUsers = async () => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/admin/users/unverified')

      if (response.data.success) {
        users.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch unverified users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchAdminUsers = async () => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/admin/users/admins')

      if (response.data.success) {
        users.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch admin users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateUserRole = async (id, role) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.patch(`/admin/users/${id}/role`, { role })

      if (response.data.success) {
        const index = users.value.findIndex(u => u.id === id)
        if (index !== -1) {
          users.value[index] = response.data.data
        }
        return response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update user role'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    users,
    currentUser,
    statistics,
    loading,
    error,
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
    fetchStatistics,
    fetchVerifiedUsers,
    fetchUnverifiedUsers,
    fetchAdminUsers,
    updateUserRole,
  }
})
