import api from './api'

export default {
    getSchedules() {
        return api.get('/schedules')
    },

    createSchedule(schedule) {
        return api.post('/schedules', schedule)
    },

    updateSchedule(id, schedule) {
        return api.put(`/schedules/${id}`, schedule)
    },

    deleteSchedule(id) {
        return api.delete(`/schedules/${id}`)
    }
}
