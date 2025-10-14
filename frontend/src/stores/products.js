import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const currentProduct = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchProducts = async () => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get('/products')

      if (response.data.success) {
        products.value = response.data.data.data || response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch products'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchProduct = async (id) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get(`/products/${id}`)

      if (response.data.success) {
        currentProduct.value = response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch product'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createProduct = async (productData) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.post('/products', productData)

      if (response.data.success) {
        products.value.unshift(response.data.data)
        return response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create product'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id, productData) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.put(`/products/${id}`, productData)

      if (response.data.success) {
        const index = products.value.findIndex(p => p.id === id)
        if (index !== -1) {
          products.value[index] = response.data.data
        }
        return response.data.data
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update product'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (id) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.delete(`/products/${id}`)

      if (response.data.success) {
        products.value = products.value.filter(p => p.id !== id)
        return true
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete product'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    products,
    currentProduct,
    loading,
    error,
    fetchProducts,
    fetchProduct,
    createProduct,
    updateProduct,
    deleteProduct,
  }
})
