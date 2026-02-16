<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Cache tozalash
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'access_filament_panel',
            'view_all_pages',
            'view_blog_pages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('Custom permissions yaratildi: ' . implode(', ', $permissions));
    }
}
