<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename legacy v3 snake_case custom permissions to v4 PascalCase format.
     */
    private array $map = [
        'access_filament_panel' => 'AccessFilamentPanel',
        'view_all_pages'        => 'ViewAllPages',
        'view_blog_pages'       => 'ViewBlogPages',
    ];

    public function up(): void
    {
        foreach ($this->map as $old => $new) {
            $permission = DB::table('permissions')->where('name', $old)->first();
            if ($permission) {
                DB::table('permissions')
                    ->where('id', $permission->id)
                    ->update(['name' => $new]);
            }
        }

        // Clear spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        foreach (array_flip($this->map) as $new => $old) {
            $permission = DB::table('permissions')->where('name', $new)->first();
            if ($permission) {
                DB::table('permissions')
                    ->where('id', $permission->id)
                    ->update(['name' => $old]);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
