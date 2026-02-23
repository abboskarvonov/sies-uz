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
        if (Schema::hasTable('pages')) {
            return;
        }

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_id')->nullable();

            $table->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->foreignId('submenu_id')->nullable()->constrained('submenus')->nullOnDelete();
            $table->foreignId('multimenu_id')->nullable()->constrained('multimenus')->nullOnDelete();

            $table->string('title_uz');
            $table->string('title_ru');
            $table->string('title_en');

            $table->text('content_uz')->nullable();
            $table->text('content_ru')->nullable();
            $table->text('content_en')->nullable();

            $table->string('slug_uz')->unique();
            $table->string('slug_ru')->unique()->nullable();
            $table->string('slug_en')->unique()->nullable();

            $table->enum('page_type', ['default', 'blog', 'department', 'faculty', 'center', 'section'])->default('default');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->string('date')->nullable();
            $table->boolean('activity')->default(false);

            $table->unsignedBigInteger('views')->default(0);

            $table->string('image')->nullable(); // asosiy rasm
            $table->json('images')->nullable();  // galereya

            $table->integer('order')->default(0);

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
        Schema::dropIfExists('pages');
    }
};
