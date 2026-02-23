<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            return;
        }

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Site name (3 languages)
            $table->string('site_name_uz')->nullable();
            $table->string('site_name_ru')->nullable();
            $table->string('site_name_en')->nullable();

            // Address (3 languages)
            $table->string('address_uz')->nullable();
            $table->string('address_ru')->nullable();
            $table->string('address_en')->nullable();

            // Phone numbers
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();

            // Email addresses
            $table->string('email_primary')->nullable();
            $table->string('email_secondary')->nullable();

            // Social links
            $table->string('telegram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();

            // External systems
            $table->string('hemis_url')->nullable();
            $table->string('arm_url')->nullable();
            $table->string('sdg_url')->nullable();

            // Logo (file path)
            $table->string('logo')->nullable();

            // Google Maps embed URL
            $table->text('map_embed_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
