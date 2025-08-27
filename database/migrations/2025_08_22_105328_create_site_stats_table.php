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
        Schema::create('site_stats', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('campus_area')->default(0);     // m2
            $table->unsignedBigInteger('green_area')->default(0);       // m2

            $table->unsignedInteger('faculties')->default(0);
            $table->unsignedInteger('departments')->default(0);
            $table->unsignedInteger('centers')->default(0);

            $table->unsignedInteger('employees')->default(0);
            $table->unsignedInteger('leadership')->default(0);
            $table->unsignedInteger('scientific')->default(0);
            $table->unsignedInteger('technical')->default(0);

            $table->unsignedInteger('students')->default(0);
            $table->unsignedInteger('male_students')->default(0);
            $table->unsignedInteger('female_students')->default(0);

            $table->unsignedInteger('teachers')->default(0);
            $table->unsignedInteger('dsi')->default(0);                 // DSc
            $table->unsignedInteger('phd_teachers')->default(0);        // PhD
            $table->unsignedInteger('professors')->default(0);          // Dotsent/prof.

            // Nashrlar
            $table->unsignedInteger('books')->default(0);
            $table->unsignedInteger('textbooks')->default(0);
            $table->unsignedInteger('study')->default(0);
            $table->unsignedInteger('methodological')->default(0);
            $table->unsignedInteger('monograph')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_stats');
    }
};