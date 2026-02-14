<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    protected $fillable = [
        'title_uz',
        'title_ru',
        'title_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'content_uz',
        'content_ru',
        'content_en',
        'image',
    ];
}
