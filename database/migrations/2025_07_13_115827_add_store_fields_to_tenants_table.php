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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name'); // Добавляем поле name для магазина
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('plan')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('theme_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name', 'user_id', 'plan', 'slug', 'theme_id']);
        });
    }
};
