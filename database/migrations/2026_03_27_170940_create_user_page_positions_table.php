<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitta xodimning bir nechta bo'lim/kafedradagi lavozimlari.
 *
 * Muammo: users.department_page_id va users.staff_category_id — faqat bitta bo'limni
 * saqlaydi. Agar xodim bir nechta lavozimda ishlasa (masalan, kafedra mudiri + markaz
 * boshlig'i), barcha lavozimlarini bu jadvalga yozamiz.
 *
 * is_primary = true — asosiy (birinchi / HEMIS da primary) lavozim.
 * users.department_page_id va users.staff_category_id — is_primary yozuvdagi
 * page_id va staff_category_id bilan hamohang saqlanadi (backward-compat uchun).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_page_positions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('page_id')
                ->constrained('pages')
                ->cascadeOnDelete();

            $table->foreignId('staff_category_id')
                ->nullable()
                ->constrained('staff_categories')
                ->nullOnDelete();

            // Lavozim (3 tilda)
            $table->string('position_uz')->nullable();
            $table->string('position_ru')->nullable();
            $table->string('position_en')->nullable();

            // Frontend uchun tartib raqami
            $table->unsignedSmallInteger('position_order')->default(99);

            // Ish shakli (asosiy, qo'shimcha, soatbay, ...)
            $table->string('employment_form')->nullable();

            // HEMIS employee type kodi (12=o'qituvchi, 11=ma'muriyat, ...)
            $table->string('hemis_employee_type_code')->nullable();

            // HEMIS dagi employee record ID (lavozimga tegishli)
            $table->string('hemis_position_id')->nullable();

            // Asosiy lavozim bayrog'i
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // Bitta xodim bitta page'da faqat bitta yozuv
            $table->unique(['user_id', 'page_id']);

            $table->index(['page_id', 'staff_category_id']);
            $table->index(['user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_page_positions');
    }
};
