<template>
  <div class="container">
    <div class="products-page">
      <div class="page-header flex justify-between align-center mb-4">
        <h1>Products</h1>
        <button @click="showModal = true" class="btn btn-primary">
          Add Product
        </button>
      </div>

      <div v-if="productStore.loading" class="loading">
        <div class="spinner"></div>
      </div>

      <div v-else-if="productStore.products.length === 0" class="card text-center">
        <p style="padding: 2rem; color: var(--text-light);">
          No products found. Click "Add Product" to create one.
        </p>
      </div>

      <div v-else class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in productStore.products" :key="product.id">
              <td>{{ product.id }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product.description || 'N/A' }}</td>
              <td>${{ product.price }}</td>
              <td>{{ product.stock }}</td>
              <td>
                <div class="flex gap-2">
                  <button @click="editProduct(product)" class="btn btn-sm btn-primary">
                    Edit
                  </button>
                  <button @click="deleteProductHandler(product.id)" class="btn btn-sm btn-danger">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal-content card" @click.stop>
          <h2 class="mb-3">{{ editMode ? 'Edit Product' : 'Add Product' }}</h2>

          <div v-if="error" class="alert alert-error">
            {{ error }}
          </div>

          <form @submit.prevent="saveProduct">
            <div class="form-group">
              <label class="form-label">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="form-control"
                placeholder="Product name"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea
                v-model="form.description"
                class="form-control"
                placeholder="Product description"
                rows="3"
              ></textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Price</label>
              <input
                v-model.number="form.price"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="0.00"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Stock</label>
              <input
                v-model.number="form.stock"
                type="number"
                class="form-control"
                placeholder="0"
                required
              />
            </div>

            <div class="flex gap-2 justify-between">
              <button type="button" @click="closeModal" class="btn">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary" :disabled="productStore.loading">
                {{ productStore.loading ? 'Saving...' : 'Save' }}
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
import { useProductStore } from '@/stores/products'

const productStore = useProductStore()
const showModal = ref(false)
const editMode = ref(false)
const error = ref(null)

const form = ref({
  id: null,
  name: '',
  description: '',
  price: 0,
  stock: 0,
})

onMounted(() => {
  productStore.fetchProducts()
})

const editProduct = (product) => {
  editMode.value = true
  form.value = { ...product }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editMode.value = false
  error.value = null
  form.value = {
    id: null,
    name: '',
    description: '',
    price: 0,
    stock: 0,
  }
}

const saveProduct = async () => {
  try {
    error.value = null

    if (editMode.value) {
      await productStore.updateProduct(form.value.id, form.value)
    } else {
      await productStore.createProduct(form.value)
    }

    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save product'
  }
}

const deleteProductHandler = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) return

  try {
    await productStore.deleteProduct(id)
  } catch (err) {
    alert('Failed to delete product')
  }
}
</script>

<style scoped>
.products-page {
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

textarea.form-control {
  resize: vertical;
  font-family: inherit;
}
</style>
