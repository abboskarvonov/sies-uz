<?php

namespace Database\Seeders;

use App\Filament\Resources\RoleResource;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $created = 0;

        foreach (RoleResource::TABS as $resources) {
            foreach ($resources as $config) {
                foreach (array_keys($config['perms']) as $permName) {
                    Permission::firstOrCreate([
                        'name'       => $permName,
                        'guard_name' => 'web',
                    ]);
                    $created++;
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("Jami {$created} ta permission yaratildi/tekshirildi.");
    }
}
