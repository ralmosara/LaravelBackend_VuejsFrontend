<template>
  <div class="schedule-container">
    <div class="header">
      <div class="header-left">
        <h1>Schedule</h1>
        <div class="date-nav">
          <button @click="previousMonth" class="nav-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
          </button>
          <h2>{{ currentMonthName }} {{ currentYear }}</h2>
          <button @click="nextMonth" class="nav-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
      </div>
      <button @click="openModal" class="add-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Event
      </button>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
      <!-- Weekday Headers -->
      <div v-for="day in weekDays" :key="day" class="weekday-header">
        {{ day }}
      </div>
      
      <!-- Calendar Days -->
      <div 
        v-for="day in calendarDays" 
        :key="day.date" 
        class="calendar-day" 
        :class="{ 
          'other-month': !day.isCurrentMonth,
          'today': isToday(day.date)
        }"
        @click="openModal(null, day.date)"
      >
        <div class="day-number">{{ day.dayNumber }}</div>
        
        <div class="events-list">
          <div 
            v-for="event in day.events" 
            :key="event.id"
            class="event-item"
            :class="event.type"
            @click.stop="openModal(event)"
          >
            <span class="event-time">{{ formatEventTime(event.start) }}</span>
            <span class="event-title">{{ event.title }}</span>
          </div>
        </div>
      </div>
    </div>

    <ScheduleModal 
      :is-open="isModalOpen" 
      :event="selectedEvent"
      :date="selectedDate"
      @close="closeModal"
      @save="handleSave"
      @delete="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useScheduleStore } from '@/stores/schedule'
import ScheduleModal from '@/components/ScheduleModal.vue'

const store = useScheduleStore()
const isModalOpen = ref(false)
const selectedEvent = ref(null)
const selectedDate = ref(null)
const currentDate = ref(new Date())

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

const currentMonthName = computed(() => months[currentDate.value.getMonth()])
const currentYear = computed(() => currentDate.value.getFullYear())

const calendarDays = computed(() => {
  const days = []
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  
  const firstDayOfMonth = new Date(year, month, 1)
  const lastDayOfMonth = new Date(year, month + 1, 0)
  
  // Previous month days
  const startingDayOfWeek = firstDayOfMonth.getDay()
  const prevMonthLastDay = new Date(year, month, 0).getDate()
  
  for (let i = startingDayOfWeek - 1; i >= 0; i--) {
    days.push({
      dayNumber: prevMonthLastDay - i,
      isCurrentMonth: false,
      date: new Date(year, month - 1, prevMonthLastDay - i),
      events: []
    })
  }
  
  // Current month days
  for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
    const date = new Date(year, month, i)
    days.push({
      dayNumber: i,
      isCurrentMonth: true,
      date: date,
      events: getEventsForDate(date)
    })
  }
  
  // Next month days to fill grid (6 rows * 7 days = 42)
  const remainingDays = 42 - days.length
  for (let i = 1; i <= remainingDays; i++) {
    days.push({
      dayNumber: i,
      isCurrentMonth: false,
      date: new Date(year, month + 1, i),
      events: []
    })
  }
  
  return days
})

const getEventsForDate = (date) => {
  return store.schedules.filter(event => {
    const eventDate = new Date(event.start)
    return eventDate.getDate() === date.getDate() &&
           eventDate.getMonth() === date.getMonth() &&
           eventDate.getFullYear() === date.getFullYear()
  })
}

const isToday = (date) => {
  const today = new Date()
  return date.getDate() === today.getDate() &&
         date.getMonth() === today.getMonth() &&
         date.getFullYear() === today.getFullYear()
}

const formatEventTime = (isoString) => {
  return new Date(isoString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

const openModal = (event = null, date = null) => {
  selectedEvent.value = event
  selectedDate.value = date || new Date()
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedEvent.value = null
  selectedDate.value = null
}

const handleSave = async (eventData) => {
  if (selectedEvent.value) {
    await store.updateSchedule(selectedEvent.value.id, eventData)
  } else {
    await store.createSchedule(eventData)
  }
}

const handleDelete = async (id) => {
  await store.deleteSchedule(id)
}

onMounted(() => {
  store.fetchSchedules()
})
</script>

<style scoped>
.schedule-container {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: #f8fafc;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.header h1 {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.date-nav {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: white;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.date-nav h2 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #334155;
  margin: 0;
  min-width: 140px;
  text-align: center;
}

.nav-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.nav-btn:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.nav-btn svg {
  width: 20px;
  height: 20px;
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
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.add-btn:hover {
  background: #4338ca;
  transform: translateY(-1px);
}

.add-btn svg {
  width: 20px;
  height: 20px;
}

/* Calendar Grid */
.calendar-grid {
  flex: 1;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-template-rows: auto 1fr;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.weekday-header {
  padding: 1rem;
  text-align: center;
  font-weight: 600;
  color: #64748b;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.calendar-day {
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  min-height: 120px;
  padding: 0.5rem;
  transition: background 0.2s;
  cursor: pointer;
}

.calendar-day:nth-child(7n) {
  border-right: none;
}

.calendar-day:hover {
  background: #f8fafc;
}

.calendar-day.other-month {
  background: #f9fafb;
  color: #cbd5e1;
}

.calendar-day.today {
  background: #f0f9ff;
}

.calendar-day.today .day-number {
  background: #0ea5e9;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: -4px;
}

.day-number {
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.events-list {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.event-item {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: flex;
  gap: 0.5rem;
  transition: all 0.2s;
}

.event-item:hover {
  transform: scale(1.02);
}

.event-item.event {
  background: #e0e7ff;
  color: #4338ca;
  border-left: 3px solid #4338ca;
}

.event-item.task {
  background: #dcfce7;
  color: #15803d;
  border-left: 3px solid #15803d;
}

.event-item.reminder {
  background: #ffedd5;
  color: #c2410c;
  border-left: 3px solid #c2410c;
}
</style>
