<?php

namespace App\Console\Commands;

use App\Models\Theme;
use App\Services\ThemeManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:manage 
                           {action : Action to perform (list|install|uninstall|activate|deactivate|scan|info)}
                           {theme? : Theme package name (required for install, uninstall, activate, deactivate, info)}
                           {--path= : Path to theme package (for install)}
                           {--force : Force operation}';

    /**
     * The console command description.
     */
    protected $description = 'Manage themes (list, install, uninstall, activate, deactivate, scan, info)';

    /**
     * Execute the console command.
     */
    public function handle(ThemeManager $themeManager): int
    {
        $action = $this->argument('action');
        $themeName = $this->argument('theme');

        try {
            switch ($action) {
                case 'list':
                    return $this->listThemes($themeManager);
                    
                case 'install':
                    return $this->installTheme($themeManager, $themeName);
                    
                case 'uninstall':
                    return $this->uninstallTheme($themeManager, $themeName);
                    
                case 'activate':
                    return $this->activateTheme($themeManager, $themeName);
                    
                case 'deactivate':
                    return $this->deactivateTheme($themeManager, $themeName);
                    
                case 'scan':
                    return $this->scanThemes($themeManager);
                    
                case 'info':
                    return $this->showThemeInfo($themeManager, $themeName);
                    
                default:
                    $this->error("Unknown action: {$action}");
                    return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Показать список всех тем
     */
    private function listThemes(ThemeManager $themeManager): int
    {
        $themes = Theme::all();
        
        if ($themes->isEmpty()) {
            $this->info('No themes found.');
            return Command::SUCCESS;
        }

        $headers = ['Name', 'Package', 'Version', 'Author', 'Active', 'System'];
        $rows = [];

        foreach ($themes as $theme) {
            $rows[] = [
                $theme->name,
                $theme->package_name,
                $theme->version,
                $theme->author ?? 'Unknown',
                $theme->is_active ? '✓' : '✗',
                $theme->is_system ? '✓' : '✗',
            ];
        }

        $this->table($headers, $rows);
        return Command::SUCCESS;
    }

    /**
     * Установить тему
     */
    private function installTheme(ThemeManager $themeManager, ?string $themeName): int
    {
        $path = $this->option('path');
        
        if (!$path) {
            $this->error('Path to theme package is required for installation.');
            return Command::FAILURE;
        }

        if (!File::exists($path)) {
            $this->error("Theme package not found at: {$path}");
            return Command::FAILURE;
        }

        $this->info("Installing theme from: {$path}");
        
        $theme = $themeManager->installTheme($path);
        
        $this->info("Theme '{$theme->name}' installed successfully!");
        $this->info("Package: {$theme->package_name}");
        $this->info("Version: {$theme->version}");
        
        return Command::SUCCESS;
    }

    /**
     * Удалить тему
     */
    private function uninstallTheme(ThemeManager $themeManager, ?string $themeName): int
    {
        if (!$themeName) {
            $this->error('Theme package name is required.');
            return Command::FAILURE;
        }

        $theme = $themeManager->getTheme($themeName);
        
        if (!$theme) {
            $this->error("Theme '{$themeName}' not found.");
            return Command::FAILURE;
        }

        if ($theme->is_system && !$this->option('force')) {
            $this->error("Cannot uninstall system theme '{$themeName}'. Use --force to override.");
            return Command::FAILURE;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to uninstall theme '{$theme->name}'?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $result = $themeManager->uninstallTheme($themeName);
        
        if ($result) {
            $this->info("Theme '{$theme->name}' uninstalled successfully!");
        } else {
            $this->error("Failed to uninstall theme '{$theme->name}'.");
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }

    /**
     * Активировать тему (для системы)
     */
    private function activateTheme(ThemeManager $themeManager, ?string $themeName): int
    {
        if (!$themeName) {
            $this->error('Theme package name is required.');
            return Command::FAILURE;
        }

        $theme = $themeManager->getTheme($themeName);
        
        if (!$theme) {
            $this->error("Theme '{$themeName}' not found.");
            return Command::FAILURE;
        }

        $theme->update(['is_active' => true]);
        
        $this->info("Theme '{$theme->name}' activated successfully!");
        return Command::SUCCESS;
    }

    /**
     * Деактивировать тему (для системы)
     */
    private function deactivateTheme(ThemeManager $themeManager, ?string $themeName): int
    {
        if (!$themeName) {
            $this->error('Theme package name is required.');
            return Command::FAILURE;
        }

        $theme = $themeManager->getTheme($themeName);
        
        if (!$theme) {
            $this->error("Theme '{$themeName}' not found.");
            return Command::FAILURE;
        }

        if ($theme->is_system && !$this->option('force')) {
            $this->error("Cannot deactivate system theme '{$themeName}'. Use --force to override.");
            return Command::FAILURE;
        }

        $theme->update(['is_active' => false]);
        
        $this->info("Theme '{$theme->name}' deactivated successfully!");
        return Command::SUCCESS;
    }

    /**
     * Сканировать директорию тем
     */
    private function scanThemes(ThemeManager $themeManager): int
    {
        $this->info('Scanning themes directory...');
        
        $themes = $themeManager->scanThemes();
        
        $this->info("Found {$themes->count()} theme(s):");
        
        foreach ($themes as $theme) {
            $this->line("  • {$theme->name} ({$theme->package_name}) v{$theme->version}");
        }
        
        return Command::SUCCESS;
    }

    /**
     * Показать информацию о теме
     */
    private function showThemeInfo(ThemeManager $themeManager, ?string $themeName): int
    {
        if (!$themeName) {
            $this->error('Theme package name is required.');
            return Command::FAILURE;
        }

        $theme = $themeManager->getTheme($themeName);
        
        if (!$theme) {
            $this->error("Theme '{$themeName}' not found.");
            return Command::FAILURE;
        }

        $this->info("Theme Information:");
        $this->line("  Name: {$theme->name}");
        $this->line("  Package: {$theme->package_name}");
        $this->line("  Version: {$theme->version}");
        $this->line("  Author: " . ($theme->author ?? 'Unknown'));
        $this->line("  Description: " . ($theme->description ?? 'No description'));
        $this->line("  Active: " . ($theme->is_active ? 'Yes' : 'No'));
        $this->line("  System: " . ($theme->is_system ? 'Yes' : 'No'));
        $this->line("  Entry Point: {$theme->entry_point}");
        
        if ($theme->features) {
            $this->line("  Features:");
            foreach ($theme->features as $feature) {
                $this->line("    • {$feature}");
            }
        }

        if ($theme->requirements) {
            $this->line("  Requirements:");
            foreach ($theme->requirements as $req => $version) {
                $this->line("    • {$req}: {$version}");
            }
        }

        // Проверка совместимости
        $compatible = $theme->isCompatible();
        $this->line("  Compatible: " . ($compatible ? 'Yes' : 'No'));
        
        // Информация о файлах
        $themePath = $theme->theme_path;
        if (File::exists($themePath)) {
            $this->line("  Theme Path: {$themePath}");
            
            $manifestPath = "{$themePath}/theme.json";
            if (File::exists($manifestPath)) {
                $this->line("  Manifest: Found");
            } else {
                $this->line("  Manifest: Missing");
            }
            
            $componentsPath = $theme->vue_components_path;
            if (File::exists($componentsPath)) {
                $componentCount = count(File::allFiles($componentsPath));
                $this->line("  Components: {$componentCount} files");
            } else {
                $this->line("  Components: Directory not found");
            }
        } else {
            $this->line("  Theme Path: Not found");
        }
        
        return Command::SUCCESS;
    }
}
