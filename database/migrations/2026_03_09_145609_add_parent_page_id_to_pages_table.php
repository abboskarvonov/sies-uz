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
        Schema::table('pages', function (Blueprint $table) {
            // Bo'lim → Markaz, Kafedra → Fakultet kabi iyerarxik bog'lanish
            $table->foreignId('parent_page_id')
                ->nullable()
                ->after('hemis_id')
                ->constrained('pages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['parent_page_id']);
            $table->dropColumn('parent_page_id');
        });
    }
};
