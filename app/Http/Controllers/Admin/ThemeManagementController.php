<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ThemeManagementController extends Controller
{
    public function __construct(
        private ThemeManager $themeManager
    ) {}

    /**
     * Получить список всех тем (для админов)
     */
    public function index(): JsonResponse
    {
        try {
            $themes = Theme::with('stores')->get();
            $stats = $this->themeManager->getThemeStats();
            
            return response()->json([
                'themes' => $themes->map(function ($theme) {
                    return [
                        'id' => $theme->id,
                        'name' => $theme->name,
                        'package_name' => $theme->package_name,
                        'version' => $theme->version,
                        'author' => $theme->author,
                        'description' => $theme->description,
                        'features' => $theme->features,
                        'is_active' => $theme->is_active,
                        'is_system' => $theme->is_system,
                        'stores_count' => $theme->stores_count,
                        'created_at' => $theme->created_at,
                        'updated_at' => $theme->updated_at,
                    ];
                }),
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Установить тему из загруженного файла
     */
    public function install(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'theme_package' => 'required|file|mimes:zip',
            'force' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('theme_package');
            $tempPath = $this->extractThemePackage($file);

            $theme = $this->themeManager->installTheme($tempPath);

            // Очищаем временные файлы
            File::deleteDirectory($tempPath);

            return response()->json([
                'message' => 'Theme installed successfully',
                'theme' => [
                    'id' => $theme->id,
                    'name' => $theme->name,
                    'package_name' => $theme->package_name,
                    'version' => $theme->version
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Удалить тему
     */
    public function uninstall(string $packageName): JsonResponse
    {
        try {
            $result = $this->themeManager->uninstallTheme($packageName);
            
            if ($result) {
                return response()->json(['message' => 'Theme uninstalled successfully']);
            }
            
            return response()->json(['error' => 'Failed to uninstall theme'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Активировать/деактивировать тему (для системы)
     */
    public function toggleActive(string $packageName): JsonResponse
    {
        try {
            $theme = Theme::where('package_name', $packageName)->firstOrFail();
            
            if ($theme->is_system && !$theme->is_active) {
                return response()->json(['error' => 'Cannot deactivate system theme'], 400);
            }

            $theme->update(['is_active' => !$theme->is_active]);

            return response()->json([
                'message' => $theme->is_active ? 'Theme activated' : 'Theme deactivated',
                'is_active' => $theme->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Обновить тему
     */
    public function update(Request $request, string $packageName): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $theme = Theme::where('package_name', $packageName)->firstOrFail();
            
            // Системные темы можно обновлять только частично
            if ($theme->is_system) {
                $allowedFields = ['description', 'is_active'];
                $updateData = $request->only($allowedFields);
            } else {
                $updateData = $request->only(['name', 'description', 'author', 'is_active']);
            }

            $theme->update($updateData);

            return response()->json([
                'message' => 'Theme updated successfully',
                'theme' => $theme
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Сканировать директорию тем и обновить базу данных
     */
    public function scan(): JsonResponse
    {
        try {
            $themes = $this->themeManager->scanThemes();
            
            return response()->json([
                'message' => 'Themes scanned successfully',
                'found_themes' => $themes->count(),
                'themes' => $themes->map(function ($theme) {
                    return [
                        'package_name' => $theme->package_name,
                        'name' => $theme->name,
                        'version' => $theme->version
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Экспортировать тему
     */
    public function export(string $packageName): JsonResponse
    {
        try {
            $theme = Theme::where('package_name', $packageName)->firstOrFail();
            
            if ($theme->is_system) {
                return response()->json(['error' => 'Cannot export system theme'], 400);
            }

            $themePath = base_path("themes/{$packageName}");
            
            if (!File::exists($themePath)) {
                return response()->json(['error' => 'Theme files not found'], 404);
            }

            // Создаем ZIP архив
            $zipPath = storage_path("app/temp/{$packageName}.zip");
            $this->createZipArchive($themePath, $zipPath);

            return response()->download($zipPath)->deleteFileAfterSend();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить логи использования тем
     */
    public function getLogs(): JsonResponse
    {
        try {
            // Здесь можно добавить логику для получения логов
            // Например, из файлов логов или отдельной таблицы
            
            return response()->json([
                'logs' => [
                    // Заглушка для логов
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Извлечь пакет темы из ZIP файла
     */
    private function extractThemePackage(UploadedFile $file): string
    {
        $tempDir = storage_path('app/temp/' . uniqid('theme_'));
        File::makeDirectory($tempDir, 0755, true);

        $zip = new \ZipArchive;
        $res = $zip->open($file->getRealPath());
        
        if ($res === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            throw new \Exception('Failed to extract theme package');
        }

        return $tempDir;
    }

    /**
     * Создать ZIP архив директории
     */
    private function createZipArchive(string $sourcePath, string $zipPath): void
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Cannot create ZIP archive');
        }

        $files = File::allFiles($sourcePath);
        
        foreach ($files as $file) {
            $relativePath = str_replace($sourcePath . '/', '', $file->getRealPath());
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        $zip->close();
    }
}
