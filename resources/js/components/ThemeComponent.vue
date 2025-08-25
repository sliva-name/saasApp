<template>
  <component 
    v-if="resolvedComponent"
    :is="resolvedComponent"
    v-bind="$attrs"
  />
  <div v-else-if="isLoading" class="theme-component-loading">
    <div class="spinner"></div>
    <span>Загрузка компонента...</span>
  </div>
  <div v-else-if="error" class="theme-component-error">
    <div class="error-icon">⚠️</div>
    <div class="error-message">
      <strong>Ошибка загрузки компонента</strong>
      <p>{{ error }}</p>
      <button @click="retry" class="retry-btn">Повторить</button>
    </div>
  </div>
  <div v-else class="theme-component-fallback">
    <div class="fallback-message">
      Компонент "{{ name }}" не найден
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineAsyncComponent } from 'vue'
import { useThemeLoader } from '../themeLoader.js'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  fallback: {
    type: [Object, String],
    default: null
  },
  lazy: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['loaded', 'error', 'fallback'])

const { currentTheme, getComponent, isLoading: themeLoading } = useThemeLoader()

const component = ref(null)
const isLoading = ref(false)
const error = ref(null)

// Вычисляем разрешенный компонент
const resolvedComponent = computed(() => {
  if (component.value) {
    return component.value
  }
  
  if (props.fallback) {
    return props.fallback
  }
  
  return null
})

// Загрузка компонента
const loadComponent = async () => {
  if (!props.name) {
    error.value = 'Component name is required'
    return
  }

  if (!currentTheme.value) {
    error.value = 'No theme loaded'
    return
  }

  isLoading.value = true
  error.value = null

  try {
    // Сначала пытаемся получить компонент из загруженной темы
    let themeComponent = getComponent(props.name)
    
    if (!themeComponent) {
      // Если компонента нет в теме, пытаемся загрузить его динамически
      if (props.lazy) {
        themeComponent = defineAsyncComponent(() => 
          import(`../themes/${currentTheme.value.info.package_name}/resources/js/components/${props.name}.vue`)
        )
      } else {
        throw new Error(`Component "${props.name}" not found in theme`)
      }
    }

    component.value = themeComponent
    emit('loaded', themeComponent)
    
  } catch (err) {
    error.value = err.message
    emit('error', err)
    
    // Если есть fallback компонент, используем его
    if (props.fallback) {
      emit('fallback', props.fallback)
    }
    
  } finally {
    isLoading.value = false
  }
}

// Повторная попытка загрузки
const retry = () => {
  loadComponent()
}

// Загружаем компонент при монтировании
onMounted(() => {
  if (currentTheme.value) {
    loadComponent()
  }
})

// Перезагружаем компонент при смене темы
watch(currentTheme, (newTheme) => {
  if (newTheme) {
    component.value = null
    loadComponent()
  }
}, { immediate: true })

// Загружаем компонент при изменении имени
watch(() => props.name, () => {
  component.value = null
  loadComponent()
})
</script>

<style scoped>
.theme-component-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  gap: 0.75rem;
  color: #64748b;
}

.spinner {
  width: 1.5rem;
  height: 1.5rem;
  border: 2px solid #e2e8f0;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.theme-component-error {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  color: #dc2626;
}

.error-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.error-message {
  flex: 1;
}

.error-message strong {
  display: block;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.error-message p {
  margin: 0.25rem 0;
  font-size: 0.875rem;
  color: #991b1b;
}

.retry-btn {
  margin-top: 0.5rem;
  padding: 0.25rem 0.75rem;
  background-color: #dc2626;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.75rem;
  transition: background-color 0.2s;
}

.retry-btn:hover {
  background-color: #b91c1c;
}

.theme-component-fallback {
  padding: 1rem;
  background-color: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 0.5rem;
  text-align: center;
  color: #64748b;
}

.fallback-message {
  font-style: italic;
}
</style>
