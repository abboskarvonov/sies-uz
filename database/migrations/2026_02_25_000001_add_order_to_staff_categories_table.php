<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_categories', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('title_en');
        });
    }

    public function down(): void
    {
        Schema::table('staff_categories', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
