<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avval pivot jadval — FK constraint bor
        Schema::dropIfExists('page_staff_member');

        // Keyin asosiy jadval
        Schema::dropIfExists('staff_members');
    }

    public function down(): void
    {
        // staff_members qayta yaratish
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->string('position_uz');
            $table->string('position_ru')->nullable();
            $table->string('position_en')->nullable();
            $table->text('content_uz')->nullable();
            $table->text('content_ru')->nullable();
            $table->text('content_en')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('staff_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // page_staff_member pivot qayta yaratish
        Schema::create('page_staff_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
