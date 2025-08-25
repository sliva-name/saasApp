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
        Schema::create('theme_configs', function (Blueprint $table) {
            $table->id();
            $table->string('theme_package_name');                // Ссылка на package_name темы
            $table->string('tenant_id');                         // ID тенанта для изоляции
            $table->json('config')->nullable();                  // Конфигурация темы для данного тенанта
            $table->json('custom_css')->nullable();              // Кастомные CSS стили
            $table->json('custom_components')->nullable();       // Переопределенные компоненты
            $table->boolean('is_active')->default(false);        // Активна ли тема для данного тенанта
            $table->timestamps();
            
            // Уникальность: один конфиг на тему на тенанта
            $table->unique(['theme_package_name', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_configs');
    }
};
