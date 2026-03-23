<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // HEMIS identifikatsiya
            $table->string('hemis_id')->nullable()->unique()->after('id');
            $table->string('hemis_uuid')->nullable()->after('hemis_id');
            $table->enum('hemis_type', ['admin', 'employee', 'student'])->default('admin')->after('hemis_uuid');

            // Qaysi kafedra/fakultet/bo'limga tegishli
            $table->foreignId('department_page_id')
                ->nullable()
                ->after('hemis_type')
                ->constrained('pages')
                ->nullOnDelete();

            // Qaysi xodimlar kategoriyasiga tegishli
            $table->foreignId('staff_category_id')
                ->nullable()
                ->after('department_page_id')
                ->constrained('staff_categories')
                ->nullOnDelete();

            // Lavozim (3 tilda)
            $table->string('position_uz')->nullable()->after('staff_category_id');
            $table->string('position_ru')->nullable()->after('position_uz');
            $table->string('position_en')->nullable()->after('position_ru');

            // Ilmiy daraja va unvon
            $table->string('academic_degree')->nullable()->after('position_en');
            $table->string('academic_rank')->nullable()->after('academic_degree');

            // Ish shakli
            $table->string('employment_form')->nullable()->after('academic_rank');

            // Frontend uchun tartib (lavozimga qarab, sync paytida to'ldiriladi)
            $table->unsignedSmallInteger('position_order')->default(99)->after('employment_form');

            // Biografiya (3 tilda)
            $table->text('content_uz')->nullable()->after('position_order');
            $table->text('content_ru')->nullable()->after('content_uz');
            $table->text('content_en')->nullable()->after('content_ru');

            // email va password — nullable qilish (HEMIS-only foydalanuvchilar uchun)
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_page_id']);
            $table->dropForeign(['staff_category_id']);
            $table->dropColumn([
                'hemis_id', 'hemis_uuid', 'hemis_type',
                'department_page_id', 'staff_category_id',
                'position_uz', 'position_ru', 'position_en',
                'academic_degree', 'academic_rank', 'employment_form',
                'position_order',
                'content_uz', 'content_ru', 'content_en',
            ]);
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
