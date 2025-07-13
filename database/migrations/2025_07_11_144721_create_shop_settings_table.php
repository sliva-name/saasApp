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
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->string('shop_email');
            $table->string('shop_phone')->nullable();
            $table->text('shop_address')->nullable();
            $table->string('currency')->default('USD');
            $table->string('timezone')->default('UTC');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->json('social_links')->nullable();
            $table->json('payment_methods')->nullable();
            $table->json('shipping_methods')->nullable();
            $table->json('tax_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
