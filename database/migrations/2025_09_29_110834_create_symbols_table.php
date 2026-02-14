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
        Schema::create('symbols', function (Blueprint $table) {
            $table->id();

            // 3 tilda title
            $table->string('title_uz');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();

            // 3 tilda slug
            $table->string('slug_uz')->nullable()->unique();
            $table->string('slug_ru')->nullable()->unique();
            $table->string('slug_en')->nullable()->unique();

            // 3 tilda content
            $table->longText('content_uz')->nullable();
            $table->longText('content_ru')->nullable();
            $table->longText('content_en')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symbols');
    }
};
