<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    // Page resurs turlari → alohida permission prefix
    private const PAGE_RESOURCES = [
        'faculty',
        'department',
        'section',
        'center',
        'boshqarma',
        'page',       // blog + default sahifalar
    ];

    // Har bir resurs uchun action'lar
    private const PAGE_ACTIONS = [
        'viewAny',
        'create',
        'update',
        'delete',
        'deleteAny',
    ];

    // Boshqa (alohida model) resurslar uchun oddiy permissionlar
    private const EXTRA_PERMISSIONS = [
        'access_filament_panel',
        'view_all_pages',    // barcha page resurslarni ko'rish (query filter)
        'view_blog_pages',   // faqat blog sahifalarni ko'rish
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Page resurslari uchun prefixli permissionlar
        foreach (self::PAGE_RESOURCES as $prefix) {
            foreach (self::PAGE_ACTIONS as $action) {
                Permission::firstOrCreate([
                    'name'       => "$prefix.$action",
                    'guard_name' => 'web',
                ]);
            }
        }

        // 2. Qo'shimcha permissionlar
        foreach (self::EXTRA_PERMISSIONS as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('Permissions yaratildi.');
        $this->command->table(
            ['Resurs', 'Permissions'],
            collect(self::PAGE_RESOURCES)->map(fn($p) => [
                $p,
                collect(self::PAGE_ACTIONS)->map(fn($a) => "$p.$a")->implode(', '),
            ])->toArray()
        );
    }
}
