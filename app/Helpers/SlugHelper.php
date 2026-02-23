<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    /**
     * Modelda unikal slug generatsiya qiladi.
     *
     * Optimizatsiya: N ta loop query o'rniga 1 ta LIKE query + PHP memory check.
     */
    public static function generateUniqueSlug(
        string $modelClass,
        string $field,
        ?string $value,
        mixed $ignoreId = null
    ): string {
        $baseSlug = Str::slug($value ?? '');

        // Bo'sh base slug bo'lsa fallback
        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        // Barcha mos sluglarni BITTA query bilan olish
        $query = $modelClass::where($field, 'LIKE', $baseSlug . '%');

        // !== null: $ignoreId = 0 bo'lsa ham ishlaydi
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        $existing = $query->pluck($field)->flip()->all(); // O(1) lookup uchun flip

        if (!isset($existing[$baseSlug])) {
            return $baseSlug;
        }

        $counter = 1;
        while (isset($existing[$baseSlug . '-' . $counter])) {
            $counter++;
        }

        return $baseSlug . '-' . $counter;
    }
}
