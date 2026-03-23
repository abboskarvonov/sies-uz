<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('staff_categories', 'hemis_employee_type_code')) {
                $table->string('hemis_employee_type_code', 5)->nullable()->after('page_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff_categories', function (Blueprint $table) {
            $table->dropColumn('hemis_employee_type_code');
        });
    }
};
