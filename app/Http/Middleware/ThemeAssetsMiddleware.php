<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ThemeAssetsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $path = $request->path();
        
        // Проверяем, является ли запрос запросом к ассетам темы
        if (str_starts_with($path, 'themes/')) {
            return $this->serveThemeAsset($request);
        }
        
        return $next($request);
    }

    /**
     * Обслуживать ассеты темы
     */
    protected function serveThemeAsset(Request $request): SymfonyResponse
    {
        $path = $request->path();
        $fullPath = base_path($path);
        
        // Проверяем безопасность пути (предотвращаем path traversal)
        if (!$this->isPathSafe($path)) {
            return response('Forbidden', 403);
        }
        
        // Проверяем, существует ли файл
        if (!File::exists($fullPath)) {
            return response('Not Found', 404);
        }
        
        // Получаем MIME тип
        $mimeType = $this->getMimeType($fullPath);
        
        // Проверяем, разрешен ли тип файла
        if (!$this->isAllowedFileType($mimeType)) {
            return response('Forbidden', 403);
        }
        
        // Читаем файл
        $content = File::get($fullPath);
        
        // Устанавливаем заголовки кеширования для статических ресурсов
        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000', // 1 год
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
        ];
        
        // Добавляем ETag для эффективного кеширования
        $etag = md5($content);
        $headers['ETag'] = '"' . $etag . '"';
        
        // Проверяем If-None-Match заголовок
        if ($request->header('If-None-Match') === '"' . $etag . '"') {
            return response('', 304, $headers);
        }
        
        return response($content, 200, $headers);
    }

    /**
     * Проверить безопасность пути
     */
    protected function isPathSafe(string $path): bool
    {
        // Запрещаем выход за пределы директории themes
        if (str_contains($path, '..') || str_contains($path, '//')) {
            return false;
        }
        
        // Проверяем, что путь начинается с themes/
        if (!str_starts_with($path, 'themes/')) {
            return false;
        }
        
        // Разрешаем только определенные расширения
        $allowedExtensions = ['js', 'css', 'vue', 'json', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions);
    }

    /**
     * Получить MIME тип файла
     */
    protected function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'vue' => 'text/x-vue-template',
            'json' => 'application/json',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
            'ttf' => 'application/font-ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Проверить, разрешен ли тип файла
     */
    protected function isAllowedFileType(string $mimeType): bool
    {
        $allowedTypes = [
            'application/javascript',
            'text/css',
            'text/x-vue-template',
            'application/json',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'application/font-woff',
            'application/font-woff2',
            'application/font-ttf',
            'application/vnd.ms-fontobject',
        ];
        
        return in_array($mimeType, $allowedTypes);
    }
}
