import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import scheduleService from '@/services/schedule'

export const useScheduleStore = defineStore('schedule', () => {
    const schedules = ref([])
    const loading = ref(false)
    const error = ref(null)

    const fetchSchedules = async () => {
        loading.value = true
        error.value = null
        try {
            const response = await scheduleService.getSchedules()
            schedules.value = response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch schedules'
            console.error('Error fetching schedules:', err)
        } finally {
            loading.value = false
        }
    }

    const createSchedule = async (scheduleData) => {
        loading.value = true
        error.value = null
        try {
            const response = await scheduleService.createSchedule(scheduleData)
            schedules.value.push(response.data.data)
            // Re-sort schedules
            schedules.value.sort((a, b) => new Date(a.start) - new Date(b.start))
            return response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create schedule'
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateSchedule = async (id, scheduleData) => {
        loading.value = true
        error.value = null
        try {
            const response = await scheduleService.updateSchedule(id, scheduleData)
            const index = schedules.value.findIndex(s => s.id === id)
            if (index !== -1) {
                schedules.value[index] = response.data.data
                // Re-sort schedules
                schedules.value.sort((a, b) => new Date(a.start) - new Date(b.start))
            }
            return response.data.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update schedule'
            throw err
        } finally {
            loading.value = false
        }
    }

    const deleteSchedule = async (id) => {
        loading.value = true
        error.value = null
        try {
            await scheduleService.deleteSchedule(id)
            schedules.value = schedules.value.filter(s => s.id !== id)
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete schedule'
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        schedules,
        loading,
        error,
        fetchSchedules,
        createSchedule,
        updateSchedule,
        deleteSchedule
    }
})
