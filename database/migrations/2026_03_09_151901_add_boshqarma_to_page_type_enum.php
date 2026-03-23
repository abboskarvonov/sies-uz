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
        // MySQL ENUM ga yangi qiymat qo'shish
        \DB::statement("ALTER TABLE pages MODIFY COLUMN page_type ENUM('default','blog','department','faculty','center','section','boshqarma') DEFAULT 'default'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE pages MODIFY COLUMN page_type ENUM('default','blog','department','faculty','center','section') DEFAULT 'default'");
    }
};
