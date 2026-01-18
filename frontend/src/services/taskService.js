import api from './api'

export default {
    getTasks() {
        return api.get('/tasks')
    },

    createTask(data) {
        return api.post('/tasks', data)
    },

    updateTask(id, data) {
        return api.put(`/tasks/${id}`, data)
    },

    deleteTask(id) {
        return api.delete(`/tasks/${id}`)
    }
}
