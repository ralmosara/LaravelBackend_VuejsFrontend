<template>
  <div class="container">
    <div class="dashboard">
      <!-- Header -->
      <div class="dashboard-header-tabs">
        <button :class="['tab', { active: activeTab === 'overview' }]" @click="activeTab = 'overview'">
          Overview
        </button>
        <button :class="['tab', { active: activeTab === 'activity' }]" @click="activeTab = 'activity'">
          Activity
        </button>
        <button :class="['tab', { active: activeTab === 'all' }]" @click="activeTab = 'all'">
          All Time
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
      </div>

      <!-- Main Content -->
      <div v-else>
        <!-- Motivational Banner -->
        <div class="motivational-banner">
          <div class="banner-content">
            <h1>You're closer than you think!</h1>
            <p>Just a couple steps from finishing up the Top 3. Complete one more course or refresh your mind to boost your rank!</p>
            <button class="btn-continue">Continue Learning</button>
          </div>
          <div class="banner-badges">
            <span class="badge-pill monthly">Monthly Churn</span>
            <span class="badge-pill designer">Top Designer</span>
            <span class="badge-pill frontend">Front-end</span>
            <span class="badge-pill master">Quiz Master</span>
            <span class="badge-pill business">Business Class</span>
            <span class="badge-pill code">Code Streak</span>
          </div>
        </div>

        <!-- Top Performers -->
        <div class="top-performers">
          <div class="performer-card" v-for="(user, index) in topUsers.slice(0, 3)" :key="user.id">
            <div class="performer-header">
              <div class="performer-avatar">
                <img :src="`https://ui-avatars.com/api/?name=${user.name}&background=random`" :alt="user.name" />
                <span class="rank-badge" :class="`rank-${index + 1}`">{{ ['🥇', '🥈', '🥉'][index] }}</span>
              </div>
              <div class="score-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                </svg>
                {{ getUserScore(user) }}
              </div>
            </div>
            <div class="performer-info">
              <h3>{{ user.name }}</h3>
              <p>{{ user.email }}</p>
              <div class="performer-stats">
                <span class="stat-item">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 10.818v2.614A3.13 3.13 0 0011.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 00-1.138-.432zM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 00-.35.13c-.14.065-.27.143-.386.233-.377.292-.514.627-.514.909 0 .184.058.39.202.592.037.051.08.102.128.152z" />
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-6a.75.75 0 01.75.75v.316a3.78 3.78 0 011.653.713c.426.33.744.74.925 1.2a.75.75 0 01-1.395.55 1.35 1.35 0 00-.447-.563 2.187 2.187 0 00-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.504 1.616-1.29 2.13-.576.377-1.261.603-1.96.696v.299a.75.75 0 11-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695-.482-.315-.857-.717-1.078-1.188a.75.75 0 111.359-.636c.08.173.245.376.54.569.313.205.706.353 1.138.432v-2.748a3.782 3.782 0 01-1.653-.713C6.9 9.433 6.5 8.681 6.5 7.875c0-.805.4-1.558 1.097-2.096a3.78 3.78 0 011.653-.713V4.75A.75.75 0 0110 4z" clip-rule="evenodd" />
                  </svg>
                  {{ getUserActivity(user) }} actions
                </span>
                <span class="stat-item time">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                  </svg>
                  {{ getUserTime(user) }}
                </span>
              </div>
              <div class="performer-badges">
                <span class="mini-badge monthly">Monthly Churn</span>
                <span class="mini-badge">{{ getUserBadge(user) }}</span>
                <span class="count-badge">+{{ index + 2 }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="leaderboard-section card">
          <div class="leaderboard-header">
            <div class="leaderboard-tabs">
              <button class="tab active">RANK</button>
              <button class="tab">USER</button>
              <button class="tab">COURSES COMPLETED</button>
              <button class="tab">STATUS</button>
              <button class="tab">BONUS</button>
              <button class="tab">POINTS</button>
            </div>
          </div>

          <div class="leaderboard-table">
            <div class="table-row" v-for="(user, index) in allUsers.slice(3)" :key="user.id">
              <div class="col-rank">
                <span class="rank-number">{{ index + 4 }}</span>
              </div>
              <div class="col-user">
                <div class="user-avatar">
                  <img :src="`https://ui-avatars.com/api/?name=${user.name}&background=random`" :alt="user.name" />
                </div>
                <span class="user-name">{{ user.name }}</span>
              </div>
              <div class="col-courses">
                {{ getUserCourses(user) }} courses
              </div>
              <div class="col-status">
                <span :class="['status-badge', getStatusClass(user)]">
                  {{ getStatusText(user) }}
                </span>
                <span class="mini-badge">{{ getUserBadge(user) }}</span>
              </div>
              <div class="col-bonus">
                <span class="bonus-amount">+{{ getUserBonus(user) }}</span>
              </div>
              <div class="col-points">
                <span class="points-amount">{{ getUserScore(user) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useUserStore } from '@/stores/users'
import api from '@/services/api'

const authStore = useAuthStore()
const userStore = useUserStore()

const loading = ref(true)
const activeTab = ref('overview')
const allUsers = ref([])
const topUsers = computed(() => allUsers.value.slice(0, 3))

// Helper functions for user data
const getUserScore = (user) => {
  // Calculate score based on user data
  const baseScore = user.id * 100
  const verifiedBonus = user.is_verified ? 200 : 0
  return baseScore + verifiedBonus + Math.floor(Math.random() * 500)
}

const getUserActivity = (user) => {
  return 5 + user.id * 2
}

const getUserTime = (user) => {
  const days = Math.floor(Math.random() * 30) + 1
  return `${days} days streak`
}

const getUserBadge = (user) => {
  const badges = ['Quiz Master', 'Top Designer', 'Code Mentor', 'Growth Hacker', 'Full Stack']
  return badges[user.id % badges.length]
}

const getUserCourses = (user) => {
  return 3 + (user.id % 8)
}

const getStatusText = (user) => {
  const statuses = ['Top Designer', 'Ivy Mentor', 'Growth Hacker', 'Code Break', 'Quiz Master']
  return statuses[user.id % statuses.length]
}

const getStatusClass = (user) => {
  const classes = ['designer', 'mentor', 'hacker', 'break', 'master']
  return classes[user.id % classes.length]
}

const getUserBonus = (user) => {
  return user.id * 10 + Math.floor(Math.random() * 50)
}

onMounted(async () => {
  loading.value = true

  try {
    // Fetch all users
    await userStore.fetchUsers()

    if (userStore.users.length > 0) {
      // Sort users by calculated score for ranking
      allUsers.value = [...userStore.users].sort((a, b) =>
        getUserScore(b) - getUserScore(a)
      )
    }
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Header Tabs */
.dashboard-header-tabs {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid #f0f0f0;
}

.dashboard-header-tabs .tab {
  padding: 1rem 0;
  border: none;
  background: transparent;
  color: #94a3b8;
  font-weight: 600;
  font-size: 0.9375rem;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all 0.3s;
}

.dashboard-header-tabs .tab.active {
  color: #1e293b;
  border-bottom-color: #6366f1;
}

/* Motivational Banner */
.motivational-banner {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border-radius: 16px;
  padding: 2.5rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.banner-content h1 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0 0 0.75rem 0;
  color: white;
}

.banner-content p {
  font-size: 1rem;
  opacity: 0.95;
  margin: 0 0 1.5rem 0;
  max-width: 500px;
  line-height: 1.6;
}

.btn-continue {
  background: white;
  color: #6366f1;
  border: none;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9375rem;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-continue:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

.banner-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  max-width: 400px;
}

.badge-pill {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8125rem;
  font-weight: 500;
  white-space: nowrap;
}

.badge-pill.monthly,
.badge-pill.designer {
  background: rgba(255, 255, 255, 0.25);
}

/* Top Performers */
.top-performers {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.performer-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.3s;
}

.performer-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.performer-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.performer-avatar {
  position: relative;
  width: 60px;
  height: 60px;
}

.performer-avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.rank-badge {
  position: absolute;
  bottom: -4px;
  right: -4px;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.score-badge {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  background: #fef3c7;
  color: #92400e;
  padding: 0.375rem 0.75rem;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.875rem;
}

.score-badge svg {
  width: 16px;
  height: 16px;
  color: #f59e0b;
}

.performer-info h3 {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.25rem 0;
}

.performer-info p {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 1rem 0;
}

.performer-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.8125rem;
  color: #64748b;
}

.stat-item svg {
  width: 14px;
  height: 14px;
}

.stat-item.time {
  color: #f97316;
}

.performer-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.mini-badge {
  background: #f1f5f9;
  color: #475569;
  padding: 0.25rem 0.625rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
}

.mini-badge.monthly {
  background: #fef3c7;
  color: #92400e;
}

.count-badge {
  background: #e0e7ff;
  color: #4338ca;
  padding: 0.25rem 0.625rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
}

/* Leaderboard Table */
.leaderboard-section {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}

.leaderboard-header {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.leaderboard-tabs {
  display: grid;
  grid-template-columns: 80px 2fr 2fr 2fr 1fr 1fr;
  gap: 1rem;
}

.leaderboard-tabs .tab {
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 700;
  text-align: left;
  padding: 0;
  cursor: default;
  letter-spacing: 0.5px;
}

.leaderboard-table {
  padding: 0;
}

.table-row {
  display: grid;
  grid-template-columns: 80px 2fr 2fr 2fr 1fr 1fr;
  gap: 1rem;
  padding: 1rem 1.5rem;
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
  transition: all 0.2s;
}

.table-row:hover {
  background: #f8fafc;
}

.table-row:last-child {
  border-bottom: none;
}

.col-rank {
  font-size: 1rem;
  font-weight: 700;
  color: #64748b;
}

.col-user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9375rem;
}

.col-courses {
  font-size: 0.875rem;
  color: #64748b;
}

.col-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-badge.designer {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.mentor {
  background: #dcfce7;
  color: #166534;
}

.status-badge.hacker {
  background: #f3e8ff;
  color: #6b21a8;
}

.status-badge.break {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.master {
  background: #ede9fe;
  color: #5b21b6;
}

.col-bonus {
  font-weight: 700;
  color: #10b981;
  font-size: 0.9375rem;
}

.col-points {
  font-weight: 700;
  color: #1e293b;
  font-size: 1rem;
}

/* Responsive */
@media (max-width: 1024px) {
  .top-performers {
    grid-template-columns: 1fr;
  }

  .motivational-banner {
    flex-direction: column;
    align-items: flex-start;
  }

  .banner-badges {
    margin-top: 1.5rem;
  }
}

@media (max-width: 768px) {
  .leaderboard-tabs,
  .table-row {
    grid-template-columns: 60px 1fr 1fr;
  }

  .col-courses,
  .col-status,
  .col-bonus {
    display: none;
  }
}
</style>
