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
        Schema::create('submenus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_id')->nullable();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('title_uz');
            $table->string('title_ru');
            $table->string('title_en');
            $table->string('slug_uz')->unique();
            $table->string('slug_ru')->unique();
            $table->string('slug_en')->unique();
            $table->string('link')->nullable();
            $table->string('type')->default('default');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order')->default(0);
            $table->string('image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submenus');
    }
};
