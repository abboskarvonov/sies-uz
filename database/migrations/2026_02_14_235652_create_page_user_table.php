<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_user')) {
            return;
        }

        Schema::create('page_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['page_id', 'user_id']);
        });

        // Mavjud page_staff_member dan ma'lumotlarni ko'chirish
        // staff_member -> user_id orqali
        DB::statement('
            INSERT IGNORE INTO page_user (page_id, user_id, created_at, updated_at)
            SELECT psm.page_id, sm.user_id, psm.created_at, psm.updated_at
            FROM page_staff_member psm
            JOIN staff_members sm ON sm.id = psm.staff_member_id
            WHERE sm.user_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('page_user');
    }
};
