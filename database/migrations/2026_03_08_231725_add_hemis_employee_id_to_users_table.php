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
        Schema::table('users', function (Blueprint $table) {
            // hemis_id          = OAuth login ID  (/oauth/api/user → id)
            // hemis_employee_id = Employee-list ID (data/employee-list → id)
            $table->string('hemis_employee_id')->nullable()->unique()->after('hemis_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hemis_employee_id');
        });
    }
};
