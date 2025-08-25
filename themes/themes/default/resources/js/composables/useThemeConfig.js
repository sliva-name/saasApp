/**
 * Composable для работы с конфигурацией темы
 */

import { ref, reactive, computed } from 'vue'

// Глобальное состояние конфигурации темы
const themeConfig = ref({
  branding: {
    site_name: 'Our Store',
    description: 'Discover amazing products at unbeatable prices',
    logo: null,
    favicon: null,
    copyright: 'All rights reserved'
  },
  colors: {
    primary: '#3b82f6',
    secondary: '#64748b',
    accent: '#ec4899',
    success: '#10b981',
    warning: '#f59e0b',
    error: '#ef4444',
    background: '#ffffff',
    surface: '#f8fafc',
    text: '#1e293b'
  },
  layout: {
    header_fixed: true,
    sidebar_enabled: true,
    footer_enabled: true,
    container_max_width: '1280px',
    padding: '1rem'
  },
  typography: {
    font_family: 'Inter, sans-serif',
    font_size_base: '16px',
    font_size_small: '14px',
    font_size_large: '18px',
    font_weight_normal: '400',
    font_weight_medium: '500',
    font_weight_bold: '700'
  },
  components: {
    button_radius: '6px',
    input_radius: '6px',
    card_radius: '8px',
    image_radius: '4px'
  },
  animations: {
    transition_duration: '200ms',
    hover_scale: '1.02',
    enable_animations: true
  }
})

// Флаг загрузки конфигурации
const isLoading = ref(false)

// Флаг ошибки
const error = ref(null)

/**
 * Основная функция composable
 */
export function useThemeConfig() {
  
  /**
   * Загрузить конфигурацию темы с сервера
   */
  const loadConfig = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await fetch('/api/theme/config')
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      
      // Мерджим полученную конфигурацию с дефолтной
      themeConfig.value = {
        ...themeConfig.value,
        ...data
      }
      
    } catch (err) {
      error.value = err.message
      console.error('Failed to load theme config:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Обновить конфигурацию темы
   */
  const updateConfig = async (newConfig) => {
    const oldConfig = { ...themeConfig.value }
    
    try {
      // Оптимистичное обновление
      themeConfig.value = {
        ...themeConfig.value,
        ...newConfig
      }
      
      const response = await fetch('/api/theme/config', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify(newConfig)
      })
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      themeConfig.value = data
      
    } catch (err) {
      // Откатываем изменения при ошибке
      themeConfig.value = oldConfig
      error.value = err.message
      console.error('Failed to update theme config:', err)
      throw err
    }
  }
  
  /**
   * Получить значение конфигурации по ключу
   */
  const getConfigValue = (key, defaultValue = null) => {
    const keys = key.split('.')
    let value = themeConfig.value
    
    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k]
      } else {
        return defaultValue
      }
    }
    
    return value
  }
  
  /**
   * Установить значение конфигурации по ключу
   */
  const setConfigValue = (key, value) => {
    const keys = key.split('.')
    const lastKey = keys.pop()
    let target = themeConfig.value
    
    // Создаем вложенную структуру если её нет
    for (const k of keys) {
      if (!target[k] || typeof target[k] !== 'object') {
        target[k] = {}
      }
      target = target[k]
    }
    
    target[lastKey] = value
  }
  
  /**
   * Сбросить конфигурацию к дефолтным значениям
   */
  const resetConfig = () => {
    themeConfig.value = {
      branding: {
        site_name: 'Our Store',
        description: 'Discover amazing products at unbeatable prices',
        logo: null,
        favicon: null,
        copyright: 'All rights reserved'
      },
      colors: {
        primary: '#3b82f6',
        secondary: '#64748b',
        accent: '#ec4899',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        background: '#ffffff',
        surface: '#f8fafc',
        text: '#1e293b'
      },
      layout: {
        header_fixed: true,
        sidebar_enabled: true,
        footer_enabled: true,
        container_max_width: '1280px',
        padding: '1rem'
      },
      typography: {
        font_family: 'Inter, sans-serif',
        font_size_base: '16px',
        font_size_small: '14px',
        font_size_large: '18px',
        font_weight_normal: '400',
        font_weight_medium: '500',
        font_weight_bold: '700'
      },
      components: {
        button_radius: '6px',
        input_radius: '6px',
        card_radius: '8px',
        image_radius: '4px'
      },
      animations: {
        transition_duration: '200ms',
        hover_scale: '1.02',
        enable_animations: true
      }
    }
  }
  
  /**
   * Экспортировать конфигурацию в JSON
   */
  const exportConfig = () => {
    return JSON.stringify(themeConfig.value, null, 2)
  }
  
  /**
   * Импортировать конфигурацию из JSON
   */
  const importConfig = (jsonString) => {
    try {
      const config = JSON.parse(jsonString)
      themeConfig.value = {
        ...themeConfig.value,
        ...config
      }
      return true
    } catch (err) {
      error.value = 'Invalid JSON format'
      return false
    }
  }
  
  // Computed свойства для удобного доступа к разделам конфигурации
  const branding = computed(() => themeConfig.value.branding || {})
  const colors = computed(() => themeConfig.value.colors || {})
  const layout = computed(() => themeConfig.value.layout || {})
  const typography = computed(() => themeConfig.value.typography || {})
  const components = computed(() => themeConfig.value.components || {})
  const animations = computed(() => themeConfig.value.animations || {})
  
  // Возвращаем API
  return {
    // Состояние
    config: themeConfig,
    isLoading,
    error,
    
    // Computed
    branding,
    colors,
    layout,
    typography,
    components,
    animations,
    
    // Методы
    loadConfig,
    updateConfig,
    getConfigValue,
    setConfigValue,
    resetConfig,
    exportConfig,
    importConfig
  }
}

/**
 * Хук для работы с цветами темы
 */
export function useThemeColors() {
  const { colors, updateConfig } = useThemeConfig()
  
  const updateColors = async (newColors) => {
    await updateConfig({ colors: { ...colors.value, ...newColors } })
  }
  
  const getColor = (colorName, opacity = 1) => {
    const color = colors.value[colorName]
    if (!color) return null
    
    if (opacity === 1) return color
    
    // Простое добавление прозрачности
    if (color.startsWith('#')) {
      const hex = color.slice(1)
      const r = parseInt(hex.substr(0, 2), 16)
      const g = parseInt(hex.substr(2, 2), 16)
      const b = parseInt(hex.substr(4, 2), 16)
      return `rgba(${r}, ${g}, ${b}, ${opacity})`
    }
    
    return color
  }
  
  return {
    colors,
    updateColors,
    getColor
  }
}

/**
 * Хук для работы с типографикой
 */
export function useThemeTypography() {
  const { typography, updateConfig } = useThemeConfig()
  
  const updateTypography = async (newTypography) => {
    await updateConfig({ typography: { ...typography.value, ...newTypography } })
  }
  
  return {
    typography,
    updateTypography
  }
}

/**
 * Хук для работы с анимациями
 */
export function useThemeAnimations() {
  const { animations, updateConfig } = useThemeConfig()
  
  const updateAnimations = async (newAnimations) => {
    await updateConfig({ animations: { ...animations.value, ...newAnimations } })
  }
  
  const getTransition = (property = 'all') => {
    const duration = animations.value.transition_duration || '200ms'
    return `${property} ${duration} ease`
  }
  
  return {
    animations,
    updateAnimations,
    getTransition
  }
}
