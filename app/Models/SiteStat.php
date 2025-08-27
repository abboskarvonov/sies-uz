<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStat extends Model
{
    protected $fillable = [
        'campus_area',
        'green_area',
        'faculties',
        'departments',
        'centers',
        'employees',
        'leadership',
        'scientific',
        'technical',
        'students',
        'male_students',
        'female_students',
        'teachers',
        'dsi',
        'phd_teachers',
        'professors',
        'books',
        'textbooks',
        'study',
        'methodological',
        'monograph',
    ];

    protected $casts = [
        'campus_area' => 'integer',
        'green_area' => 'integer',
        'faculties' => 'integer',
        'departments' => 'integer',
        'centers' => 'integer',
        'employees' => 'integer',
        'leadership' => 'integer',
        'scientific' => 'integer',
        'technical' => 'integer',
        'students' => 'integer',
        'male_students' => 'integer',
        'female_students' => 'integer',
        'teachers' => 'integer',
        'dsi' => 'integer',
        'phd_teachers' => 'integer',
        'professors' => 'integer',
        'books' => 'integer',
        'textbooks' => 'integer',
        'study' => 'integer',
        'methodological' => 'integer',
        'monograph' => 'integer',
    ];
}