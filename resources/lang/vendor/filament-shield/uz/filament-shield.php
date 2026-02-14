<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nomi',
    'column.guard_name' => 'Guard nomi',
    'column.team' => 'Jamoa',
    'column.roles' => 'Rollar',
    'column.permissions' => 'Huquqlar',
    'column.updated_at' => 'Yangilangan',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nomi',
    'field.guard_name' => 'Guard nomi',
    'field.permissions' => 'Huquqlar',
    'field.team' => 'Jamoa',
    'field.team.placeholder' => 'Jamoani tanlang ...',
    'field.select_all.name' => 'Barchasini tanlash',
    'field.select_all.message' => 'Ushbu rol uchun barcha huquqlarni yoqish/o\'chirish',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'System',
    'nav.role.label' => 'Rollar',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollar',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Ob\'yektlar',
    'resources' => 'Resurslar',
    'widgets' => 'Vidjetlar',
    'pages' => 'Sahifalar',
    'custom' => 'Maxsus huquqlar',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Sizda kirish huquqi yo\'q',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Ko\'rish',
        'view_any' => 'Barchasini ko\'rish',
        'create' => 'Yaratish',
        'update' => 'Tahrirlash',
        'delete' => 'O\'chirish',
        'delete_any' => 'Barchasini o\'chirish',
        'force_delete' => 'Majburiy o\'chirish',
        'force_delete_any' => 'Barchasini majburiy o\'chirish',
        'restore' => 'Tiklash',
        'reorder' => 'Tartibni o\'zgartirish',
        'restore_any' => 'Barchasini tiklash',
        'replicate' => 'Nusxalash',
    ],
];
