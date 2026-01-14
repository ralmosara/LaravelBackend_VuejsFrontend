<template>
  <div class="page-container">
    <h1>Settings</h1>
    <p class="subtitle">Configure your application preferences</p>
    
    <div class="coming-soon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="3"/>
        <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
      </svg>
      <h2>Coming Soon</h2>
      <p>Settings configuration will be available shortly</p>
    </div>
  </div>
</template>

<script setup>
// Settings logic will be implemented here
</script>

<style scoped>
.page-container {
  padding: 2rem;
}

h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #64748b;
  margin-bottom: 2rem;
}

.coming-soon {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  background: #f8fafc;
  border-radius: 12px;
  border: 2px dashed #e2e8f0;
}

.coming-soon svg {
  width: 80px;
  height: 80px;
  color: #cbd5e1;
  margin-bottom: 1.5rem;
}

.coming-soon h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 0.5rem;
}

.coming-soon p {
  color: #64748b;
  font-size: 1rem;
}
</style>
