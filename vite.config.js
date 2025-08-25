import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { glob } from 'glob';
import path from 'path';
import fs from 'fs';

// Получаем все entry points тем
const getThemeEntries = () => {
    const entries = {};
    
    // Сканируем директории тем в структуре themes/themes/
    const themeDirectories = glob.sync('themes/themes/*/resources/js/index.js');
    
    themeDirectories.forEach(themeEntry => {
        const themePath = themeEntry.replace('/resources/js/index.js', '');
        const themeName = themePath.split('/').pop(); // Получаем название темы (например, "default")
        entries[`themes/${themeName}/index`] = themeEntry;
        
        // Также добавляем CSS файлы тем если есть
        const themeCss = themeEntry.replace('/index.js', '/styles/theme.css');
        if (fs.existsSync(themeCss)) {
            entries[`themes/${themeName}/theme`] = themeCss;
        }
    });
    
    return entries;
};

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/tenant.js',  // только для витрины (Vue)
                ...Object.values(getThemeEntries()), // Добавляем entry points тем
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@themes': path.resolve(__dirname, 'themes'),
        },
    },
    build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                tenant: 'resources/js/tenant.js',
                'app-css': 'resources/css/app.css',
                ...getThemeEntries(),
            },
            output: {
                // Настройка выходных файлов для тем
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.includes('themes/')) {
                        return 'themes/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
                chunkFileNames: (chunkInfo) => {
                    if (chunkInfo.name && chunkInfo.name.includes('themes/')) {
                        return 'themes/[name]-[hash].js';
                    }
                    return 'assets/[name]-[hash].js';
                },
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name && chunkInfo.name.includes('themes/')) {
                        return 'themes/[name]-[hash].js';
                    }
                    return 'assets/[name]-[hash].js';
                },
            },
        },
    },
    // Оптимизация для тем
    optimizeDeps: {
        include: ['vue', '@vueuse/core'],
        exclude: [], // Исключаем пакеты тем из предварительной сборки
    },
});
