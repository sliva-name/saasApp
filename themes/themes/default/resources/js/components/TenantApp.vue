<template>
  <div 
    class="theme-default min-h-screen"
    :style="cssVariables"
  >
    <!-- Шапка -->
    <Header />
    
    <!-- Навигация -->
    <Navigation />
    
    <!-- Основной контент -->
    <main 
      class="main-content"
      :class="{ 'with-fixed-header': config.layout.header_fixed }"
    >
      <div class="container mx-auto px-4 py-8">
        <div class="animate-fade-in">
          <router-view />
        </div>
      </div>
    </main>
    
    <!-- Футер -->
    <Footer v-if="config.layout.footer_enabled" />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import Header from './Header.vue'
import Navigation from './Navigation.vue'
import Footer from './Footer.vue'
import { useThemeConfig } from '../composables/useThemeConfig'

// Получаем конфигурацию темы
const { config, updateConfig } = useThemeConfig()

// Вычисляем CSS переменные на основе конфигурации
const cssVariables = computed(() => {
  const colors = config.value.colors || {}
  const typography = config.value.typography || {}
  
  return {
    '--color-primary': colors.primary,
    '--color-secondary': colors.secondary,
    '--color-accent': colors.accent,
    '--color-success': colors.success,
    '--color-warning': colors.warning,
    '--color-error': colors.error,
    '--color-background': colors.background,
    '--color-surface': colors.surface,
    '--color-text': colors.text,
    '--font-family': typography.font_family,
    '--font-size-base': typography.font_size_base,
    '--transition-duration': config.value.animations?.transition_duration || '200ms',
  }
})

onMounted(() => {
  // Применяем тему к body
  document.body.className = 'theme-default'
})
</script>

<style scoped>
.theme-default {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-family: var(--font-family, 'Inter, sans-serif');
  background-color: var(--color-background, #ffffff);
  color: var(--color-text, #1e293b);
}

.main-content {
  flex: 1;
}

.main-content.with-fixed-header {
  padding-top: 80px; /* Высота фиксированной шапки */
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Переходы между страницами */
.router-view-enter-active,
.router-view-leave-active {
  transition: all var(--transition-duration, 200ms) ease;
}

.router-view-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.router-view-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Адаптивность */
@media (max-width: 768px) {
  .main-content.with-fixed-header {
    padding-top: 60px;
  }
}
</style>

<style>
/* Глобальные стили для темы */
.theme-default {
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.theme-default .btn-primary {
  background-color: var(--color-primary);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all var(--transition-duration);
  border: none;
  cursor: pointer;
}

.theme-default .btn-primary:hover {
  background-color: color-mix(in srgb, var(--color-primary) 90%, black);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.theme-default .btn-secondary {
  background-color: var(--color-surface);
  color: var(--color-text);
  border: 1px solid var(--color-secondary);
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all var(--transition-duration);
  cursor: pointer;
}

.theme-default .btn-secondary:hover {
  background-color: var(--color-secondary);
  color: white;
}

.theme-default .card {
  background-color: var(--color-surface);
  border-radius: 0.5rem;
  box-shadow: var(--shadow);
  overflow: hidden;
  transition: all var(--transition-duration);
}

.theme-default .card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}
</style>
