import { defineStore } from 'pinia'
import { ref } from 'vue'
import taskService from '@/services/taskService'

export const useTaskStore = defineStore('tasks', () => {
    const tasks = ref([])
    const loading = ref(false)
    const error = ref(null)

    const fetchTasks = async () => {
        loading.value = true
        error.value = null
        try {
            const response = await taskService.getTasks()
            tasks.value = response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch tasks'
            console.error(err)
        } finally {
            loading.value = false
        }
    }

    const createTask = async (taskData) => {
        loading.value = true
        error.value = null
        try {
            const response = await taskService.createTask(taskData)
            tasks.value.unshift(response.data.data)
            return response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create task'
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateTask = async (id, taskData) => {
        loading.value = true
        error.value = null
        try {
            const response = await taskService.updateTask(id, taskData)
            const index = tasks.value.findIndex(t => t.id === id)
            if (index !== -1) {
                tasks.value[index] = response.data.data
            }
            return response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update task'
            throw err
        } finally {
            loading.value = false
        }
    }

    const deleteTask = async (id) => {
        loading.value = true
        error.value = null
        try {
            await taskService.deleteTask(id)
            tasks.value = tasks.value.filter(t => t.id !== id)
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete task'
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        tasks,
        loading,
        error,
        fetchTasks,
        createTask,
        updateTask,
        deleteTask
    }
})
