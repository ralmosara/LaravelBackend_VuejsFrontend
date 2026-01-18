<template>
  <div v-if="isOpen" class="modal-backdrop" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ isEditing ? 'Edit Task' : 'New Task' }}</h3>
        <button class="close-btn" @click="close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="modal-body">
        <div class="form-group">
          <label>Title</label>
          <input 
            v-model="form.title" 
            type="text" 
            placeholder="Task title"
            required
            class="form-input"
          >
        </div>

        <div class="form-group">
          <label>Due Date</label>
          <input 
            v-model="form.due_date" 
            type="date" 
            class="form-input"
          >
        </div>
        
        <div class="form-group checkbox-group">
           <label class="checkbox-label">
            <input type="checkbox" v-model="form.is_completed">
            Completed
           </label>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea 
            v-model="form.description" 
            rows="3" 
            placeholder="Add details"
            class="form-input"
          ></textarea>
        </div>

        <div class="modal-footer">
          <button v-if="isEditing" type="button" @click="handleDelete" class="delete-btn">
            Delete
          </button>
          <div class="action-buttons">
            <button type="button" @click="close" class="cancel-btn">Cancel</button>
            <button type="submit" class="save-btn" :disabled="loading">
              {{ loading ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  task: Object
})

const emit = defineEmits(['close', 'save', 'delete'])

const loading = ref(false)
const isEditing = ref(false)

const form = ref({
  title: '',
  description: '',
  due_date: '',
  is_completed: false
})

// Initialize form when modal opens
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    if (props.task) {
      // Editing existing task
      isEditing.value = true
      form.value = {
        ...props.task, // Includes id
        // Ensure due_date is nicely formatted if it exists (assuming YYYY-MM-DD from backend)
        due_date: props.task.due_date ? props.task.due_date : '', 
      }
    } else {
      // Creating new task
      isEditing.value = false
      form.value = {
        title: '',
        description: '',
        due_date: '',
        is_completed: false
      }
    }
  }
})

const close = () => {
  emit('close')
}

const handleSubmit = async () => {
  loading.value = true
  try {
    await emit('save', {
      ...form.value,
      id: props.task?.id
    })
    close()
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const handleDelete = async () => {
  if (confirm('Are you sure you want to delete this task?')) {
    loading.value = true
    try {
      await emit('delete', props.task.id)
      close()
    } catch (error) {
      console.error(error)
    } finally {
      loading.value = false
    }
  }
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  width: 100%;
  max-width: 500px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  animation: modal-slide 0.3s ease-out;
}

@keyframes modal-slide {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8fafc;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.close-btn {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  transition: all 0.2s;
  background: #f8fafc;
}

.form-input:focus {
  outline: none;
  border-color: #6366f1;
  background: white;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

textarea.form-input {
  resize: vertical;
  min-height: 100px;
}

.checkbox-group {
    display: flex;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input {
    width: 1.2rem;
    height: 1.2rem;
}

.modal-footer {
  margin-top: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
  margin-left: auto;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: white;
  border: 1px solid #e2e8f0;
  color: #64748b;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background: #f8fafc;
  color: #475569;
  border-color: #cbd5e1;
}

.save-btn {
  padding: 0.75rem 1.5rem;
  background: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);
}

.save-btn:hover {
  background: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1), 0 4px 6px -2px rgba(79, 70, 229, 0.05);
}

.save-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.delete-btn {
  padding: 0.75rem 1.5rem;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.delete-btn:hover {
  background: #fecaca;
  color: #b91c1c;
}
</style>
