<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                    // Имя темы, например "Classic"
            $table->string('slug')->unique();                    // slug для url и внутреннего использования
            $table->string('package_name')->unique();            // Имя пакета, например "themes/classic"
            $table->string('version')->default('1.0.0');         // Версия темы
            $table->string('author')->nullable();                // Автор темы
            $table->string('preview_image')->nullable();         // путь к превью (опционально)
            $table->text('description')->nullable();             // Описание темы
            $table->json('features')->nullable();                // Доступные функции темы
            $table->json('config_schema')->nullable();           // Схема конфигурации
            $table->json('requirements')->nullable();            // Требования (минимальная версия PHP, Laravel и т.д.)
            $table->string('entry_point')->default('index.js');  // Точка входа для Vue компонентов
            $table->boolean('is_active')->default(true);         // Активна ли тема
            $table->boolean('is_system')->default(false);        // Системная тема (нельзя удалить)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
