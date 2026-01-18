<template>
  <div class="page-container">
    <div class="header">
      <div>
        <h1>Tasks</h1>
        <p class="subtitle">Manage your daily tasks</p>
      </div>
      <button @click="openCreateModal" class="add-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Task
      </button>
    </div>

    <div v-if="taskStore.loading && !taskStore.tasks.length" class="loading-state">
      <div class="spinner"></div>
      <p>Loading tasks...</p>
    </div>

    <div v-else-if="taskStore.tasks.length === 0" class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
      <h3>No tasks yet</h3>
      <p>Create your first task to get started</p>
      <button @click="openCreateModal" class="create-btn">Create Task</button>
    </div>

    <div v-else class="tasks-grid">
      <div 
        v-for="task in taskStore.tasks" 
        :key="task.id" 
        class="task-card"
        :class="{ 'completed': task.is_completed }"
        @click="openEditModal(task)"
      >
        <div class="task-content">
          <div class="task-header">
            <div class="checkbox-wrapper" @click.stop>
                <input 
                    type="checkbox" 
                    :checked="task.is_completed" 
                    @change="toggleComplete(task)"
                >
            </div>
            <h3 class="task-title">{{ task.title }}</h3>
          </div>
          <p v-if="task.description" class="task-description">{{ task.description }}</p>
          <div class="task-meta">
            <span v-if="task.due_date" class="due-date">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              {{ formatDate(task.due_date) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <TaskModal
      :is-open="isModalOpen"
      :task="selectedTask"
      @close="closeModal"
      @save="handleSave"
      @delete="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import TaskModal from '@/components/Tasks/TaskModal.vue'

const taskStore = useTaskStore()
const isModalOpen = ref(false)
const selectedTask = ref(null)

onMounted(() => {
  taskStore.fetchTasks()
})

const openCreateModal = () => {
  selectedTask.value = null
  isModalOpen.value = true
}

const openEditModal = (task) => {
  selectedTask.value = task
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedTask.value = null
}

const handleSave = async (taskData) => {
  if (taskData.id) {
    await taskStore.updateTask(taskData.id, taskData)
  } else {
    await taskStore.createTask(taskData)
  }
}

const handleDelete = async (taskId) => {
  await taskStore.deleteTask(taskId)
}

const toggleComplete = async (task) => {
    await taskStore.updateTask(task.id, { is_completed: !task.is_completed })
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>

<style scoped>
.page-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2rem;
}

h1 {
  font-size: 2.25rem;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.subtitle {
  color: #64748b;
  font-size: 1.1rem;
  margin: 0;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #4f46e5;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);
}

.add-btn:hover {
  background: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1), 0 4px 6px -2px rgba(79, 70, 229, 0.05);
}

.add-btn svg {
  width: 20px;
  height: 20px;
}

.tasks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.task-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  border: 1px solid #f1f5f9;
  transition: all 0.2s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.task-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
  border-color: #e2e8f0;
}

.task-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    padding-top: 0.25rem;
}

.checkbox-wrapper input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
}

.task-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #334155;
  margin: 0;
  line-height: 1.4;
}

.task-card.completed .task-title {
    text-decoration: line-through;
    color: #94a3b8;
}

.task-description {
  color: #64748b;
  font-size: 0.95rem;
  margin-bottom: 1rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.task-meta {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #f8fafc;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.due-date {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
}

.due-date svg {
  width: 16px;
  height: 16px;
  color: #94a3b8;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: #f8fafc;
  border-radius: 16px;
  border: 2px dashed #e2e8f0;
}

.empty-state svg {
  width: 64px;
  height: 64px;
  color: #cbd5e1;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #64748b;
  margin-bottom: 2rem;
}

.create-btn {
  padding: 0.75rem 1.5rem;
  background: white;
  color: #4f46e5;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.create-btn:hover {
  border-color: #4f46e5;
  background: #eff6ff;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  gap: 1rem;
  color: #64748b;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
