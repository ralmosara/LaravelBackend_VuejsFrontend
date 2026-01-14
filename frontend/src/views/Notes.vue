<template>
  <div class="page-container">
    <h1>Notes</h1>
    <p class="subtitle">Create and manage your notes</p>
    
    <div class="coming-soon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
      <h2>Coming Soon</h2>
      <p>Note-taking features will be available shortly</p>
    </div>
  </div>
</template>

<script setup>
// Notes logic will be implemented here
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
