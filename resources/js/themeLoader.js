/**
 * Theme Loader
 * 
 * Динамический загрузчик тем с поддержкой hot reload
 * и изоляции компонентов
 */

import { ref, reactive, markRaw } from 'vue'

class ThemeLoader {
  constructor() {
    this.currentTheme = ref(null)
    this.isLoading = ref(false)
    this.error = ref(null)
    this.themeCache = new Map()
    this.componentCache = new Map()
  }

  /**
   * Загрузить тему по package_name
   */
  async loadTheme(packageName, options = {}) {
    this.isLoading.value = true
    this.error.value = null

    try {
      // Проверяем кеш если не форсируем перезагрузку
      if (!options.forceReload && this.themeCache.has(packageName)) {
        this.currentTheme.value = this.themeCache.get(packageName)
        return this.currentTheme.value
      }

      // Получаем информацию о теме с сервера
      const themeInfo = await this.fetchThemeInfo(packageName)
      
      if (!themeInfo) {
        throw new Error(`Theme ${packageName} not found`)
      }

      // Загружаем JavaScript модуль темы
      const themeModule = await this.loadThemeModule(packageName, themeInfo.entry_point)
      
      // Создаем объект темы
      const theme = {
        info: themeInfo,
        module: themeModule,
        components: new Map(),
        routes: themeModule.routes || [],
        hooks: themeModule.hooks || {},
        config: themeModule.config || {}
      }

      // Загружаем компоненты темы из API
      await this.loadThemeComponentsFromAPI(theme)

      // Кешируем тему
      this.themeCache.set(packageName, markRaw(theme))
      this.currentTheme.value = theme

      // Выполняем хук beforeMount если есть
      if (theme.hooks.beforeMount) {
        await theme.hooks.beforeMount()
      }

      return theme

    } catch (error) {
      this.error.value = error.message
      console.error('[ThemeLoader] Failed to load theme:', error)
      throw error
    } finally {
      this.isLoading.value = false
    }
  }

  /**
   * Получить информацию о теме с сервера
   */
  async fetchThemeInfo(packageName) {
    try {
      const response = await fetch(`/api/themes/${encodeURIComponent(packageName)}`)
      
      if (!response.ok) {
        if (response.status === 404) {
          return null
        }
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      return await response.json()
    } catch (error) {
      console.error('[ThemeLoader] Failed to fetch theme info:', error)
      throw error
    }
  }

  /**
   * Загрузить JavaScript модуль темы
   */
  async loadThemeModule(packageName, entryPoint = 'index.js') {
    try {
      // Путь к модулю темы
      const modulePath = `/themes/${packageName}/resources/js/${entryPoint}`
      
      // Динамический импорт с добавлением timestamp для обхода кеша
      const timestamp = Date.now()
      const module = await import(/* @vite-ignore */ `${modulePath}?t=${timestamp}`)
      
      return module.default || module
    } catch (error) {
      console.error('[ThemeLoader] Failed to load theme module:', error)
      throw new Error(`Failed to load theme module: ${error.message}`)
    }
  }

  /**
   * Загрузить компоненты темы из API
   */
  async loadThemeComponentsFromAPI(theme) {
    try {
      const response = await fetch('/api/theme/components')
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      const components = await response.json()
      
      await this.loadThemeComponents(theme, components)
    } catch (error) {
      console.error('[ThemeLoader] Failed to load components from API:', error)
      // Fallback к пустому объекту компонентов, если API недоступен
      console.warn('[ThemeLoader] Using empty components due to API failure')
    }
  }

  /**
   * Загрузить компоненты темы
   */
  async loadThemeComponents(theme, components) {
    const loadPromises = []

    for (const [componentName, componentPath] of Object.entries(components)) {
      loadPromises.push(
        this.loadComponent(theme.info.package_name, componentName, componentPath)
          .then(component => {
            theme.components.set(componentName, markRaw(component))
          })
          .catch(error => {
            console.warn(`[ThemeLoader] Failed to load component ${componentName}:`, error)
          })
      )
    }

    await Promise.allSettled(loadPromises)
  }

  /**
   * Загрузить отдельный компонент темы
   */
  async loadComponent(packageName, componentName, componentPath) {
    const cacheKey = `${packageName}:${componentName}`
    
    if (this.componentCache.has(cacheKey)) {
      return this.componentCache.get(cacheKey)
    }

    try {
      // Если componentPath уже полный путь, используем его
      let fullPath
      if (typeof componentPath === 'string' && componentPath.startsWith('/themes/')) {
        fullPath = componentPath
      } else if (typeof componentPath === 'string' && componentPath.endsWith('.vue')) {
        // Если это относительный путь к .vue файлу, добавляем базовый путь темы
        fullPath = `/themes/${packageName}/resources/js/components/${componentPath}`
      } else if (typeof componentPath === 'string') {
        // Если это просто название файла без расширения
        fullPath = `/themes/${packageName}/resources/js/components/${componentPath}.vue`
      } else {
        // Если componentPath - не строка, строим путь по имени компонента
        fullPath = `/themes/${packageName}/resources/js/components/${componentName}.vue`
      }
      
      // Динамический импорт компонента
      const timestamp = Date.now()
      const module = await import(/* @vite-ignore */ `${fullPath}?t=${timestamp}`)
      const component = module.default || module

      // Кешируем компонент
      this.componentCache.set(cacheKey, markRaw(component))
      
      return component
    } catch (error) {
      console.error(`[ThemeLoader] Failed to load component ${componentName}:`, error)
      throw error
    }
  }

  /**
   * Получить компонент из текущей темы
   */
  getComponent(componentName) {
    if (!this.currentTheme.value) {
      console.warn('[ThemeLoader] No theme loaded')
      return null
    }

    const component = this.currentTheme.value.components.get(componentName)
    
    if (!component) {
      console.warn(`[ThemeLoader] Component ${componentName} not found in current theme`)
      return null
    }

    return component
  }

  /**
   * Получить все компоненты текущей темы
   */
  getComponents() {
    if (!this.currentTheme.value) {
      return new Map()
    }

    return this.currentTheme.value.components
  }

  /**
   * Получить маршруты текущей темы
   */
  getRoutes() {
    if (!this.currentTheme.value) {
      return []
    }

    return this.currentTheme.value.routes || []
  }

  /**
   * Переключиться на другую тему
   */
  async switchTheme(packageName, options = {}) {
    const currentTheme = this.currentTheme.value

    try {
      // Выполняем хук beforeUnmount текущей темы
      if (currentTheme?.hooks?.beforeUnmount) {
        await currentTheme.hooks.beforeUnmount()
      }

      // Загружаем новую тему
      const newTheme = await this.loadTheme(packageName, options)

      // Выполняем хук mounted новой темы
      if (newTheme.hooks.mounted) {
        await newTheme.hooks.mounted()
      }

      return newTheme
    } catch (error) {
      // При ошибке возвращаем предыдущую тему
      this.currentTheme.value = currentTheme
      throw error
    }
  }

  /**
   * Очистить кеш тем
   */
  clearCache() {
    this.themeCache.clear()
    this.componentCache.clear()
  }

  /**
   * Проверить, загружена ли тема
   */
  isThemeLoaded(packageName) {
    return this.themeCache.has(packageName)
  }

  /**
   * Получить список загруженных тем
   */
  getLoadedThemes() {
    return Array.from(this.themeCache.keys())
  }

  /**
   * Перезагрузить текущую тему (полезно для разработки)
   */
  async reloadCurrentTheme() {
    if (!this.currentTheme.value) {
      throw new Error('No theme is currently loaded')
    }

    const packageName = this.currentTheme.value.info.package_name
    return await this.switchTheme(packageName, { forceReload: true })
  }

  /**
   * Получить информацию о текущей теме
   */
  getCurrentThemeInfo() {
    return this.currentTheme.value?.info || null
  }

  /**
   * Проверить совместимость темы
   */
  async checkThemeCompatibility(packageName) {
    try {
      const themeInfo = await this.fetchThemeInfo(packageName)
      
      if (!themeInfo) {
        return { compatible: false, reason: 'Theme not found' }
      }

      const requirements = themeInfo.requirements || {}
      
      // Здесь можно добавить проверки совместимости
      // Например, проверка версии Vue, Laravel и т.д.
      
      return { compatible: true, requirements }
    } catch (error) {
      return { compatible: false, reason: error.message }
    }
  }
}

// Создаем единственный экземпляр загрузчика
const themeLoader = new ThemeLoader()

/**
 * Composable для работы с темами
 */
export function useThemeLoader() {
  return {
    // Состояние
    currentTheme: themeLoader.currentTheme,
    isLoading: themeLoader.isLoading,
    error: themeLoader.error,
    
    // Методы
    loadTheme: (packageName, options) => themeLoader.loadTheme(packageName, options),
    switchTheme: (packageName, options) => themeLoader.switchTheme(packageName, options),
    getComponent: (componentName) => themeLoader.getComponent(componentName),
    getComponents: () => themeLoader.getComponents(),
    getRoutes: () => themeLoader.getRoutes(),
    clearCache: () => themeLoader.clearCache(),
    reloadCurrentTheme: () => themeLoader.reloadCurrentTheme(),
    getCurrentThemeInfo: () => themeLoader.getCurrentThemeInfo(),
    checkThemeCompatibility: (packageName) => themeLoader.checkThemeCompatibility(packageName),
    
    // Утилиты
    isThemeLoaded: (packageName) => themeLoader.isThemeLoaded(packageName),
    getLoadedThemes: () => themeLoader.getLoadedThemes(),
  }
}

// Экспорт класса для расширенного использования
export { ThemeLoader }

// Экспорт экземпляра по умолчанию
export default themeLoader
