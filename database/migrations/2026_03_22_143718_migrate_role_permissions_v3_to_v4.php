<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migrate role permission assignments from Shield v3 (snake_case) to Shield v4 (PascalCase:Model).
 *
 * v3 format: view_any_page, create_menu, delete_any_activity ...
 * v4 format: ViewAny:Page, Create:Menu, Delete:Activity ...
 */
return new class extends Migration
{
    /** v3 prefix → v4 action (order matters: longer prefixes first) */
    private array $prefixMap = [
        'view_any_'          => 'ViewAny',
        'delete_any_'        => 'Delete',       // v4 uses Delete for both single & bulk
        'force_delete_any_'  => 'ForceDeleteAny',
        'restore_any_'       => 'RestoreAny',
        'view_'              => 'View',
        'create_'            => 'Create',
        'update_'            => 'Update',
        'delete_'            => 'Delete',
        'force_delete_'      => 'ForceDelete',
        'restore_'           => 'Restore',
        'replicate_'         => 'Replicate',
        'reorder_'           => 'Reorder',
    ];

    /** v3 resource suffix → v4 model name */
    private array $resourceMap = [
        'page::file'    => 'PageFile',
        'site::stat'    => 'SiteStat',
        'staff::member' => 'StaffMember',
        'activity'      => 'Activity',
        'menu'          => 'Menu',
        'multimenu'     => 'Multimenu',
        'page'          => 'Page',
        'role'          => 'Role',
        'submenu'       => 'Submenu',
        'symbol'        => 'Symbol',
        'tag'           => 'Tag',
        'user'          => 'User',
        'site_settings' => 'SiteSettings',
        'site_stat'     => 'SiteStat',
        'staff_member'  => 'StaffMember',
        'page_file'     => 'PageFile',
    ];

    /** Skip permissions already migrated or not resource-related */
    private array $skip = [
        'AccessFilamentPanel', 'ViewAllPages', 'ViewBlogPages', 'panel_user', 'access_filament_panel',
    ];

    public function up(): void
    {
        $permissionsTable = config('permission.table_names.permissions', 'permissions');
        $rolePermissionsTable = config('permission.table_names.role_has_permissions', 'role_has_permissions');

        // Build a lookup of all existing v4 permission names → IDs
        $v4Perms = DB::table($permissionsTable)
            ->where('name', 'like', '%:%')
            ->pluck('id', 'name');

        // Get all v3-format permissions (no colon, not in skip list)
        $v3Perms = DB::table($permissionsTable)
            ->get()
            ->filter(fn($p) => !str_contains($p->name, ':') && !in_array($p->name, $this->skip));

        // Build v3_id → v4_id map
        $idMap = [];
        foreach ($v3Perms as $perm) {
            $v4Name = $this->toV4Name($perm->name);
            if ($v4Name && isset($v4Perms[$v4Name])) {
                $idMap[$perm->id] = $v4Perms[$v4Name];
            }
        }

        if (empty($idMap)) {
            return;
        }

        // Get all roles
        $roles = DB::table(config('permission.table_names.roles', 'roles'))->get();

        foreach ($roles as $role) {
            // Get this role's v3 permission IDs
            $v3Assigned = DB::table($rolePermissionsTable)
                ->where('role_id', $role->id)
                ->whereIn('permission_id', array_keys($idMap))
                ->pluck('permission_id');

            if ($v3Assigned->isEmpty()) {
                continue;
            }

            foreach ($v3Assigned as $v3Id) {
                $v4Id = $idMap[$v3Id];

                // Add v4 permission if not already assigned
                $exists = DB::table($rolePermissionsTable)
                    ->where('role_id', $role->id)
                    ->where('permission_id', $v4Id)
                    ->exists();

                if (!$exists) {
                    DB::table($rolePermissionsTable)->insert([
                        'role_id'       => $role->id,
                        'permission_id' => $v4Id,
                    ]);
                }

                // Remove old v3 assignment
                DB::table($rolePermissionsTable)
                    ->where('role_id', $role->id)
                    ->where('permission_id', $v3Id)
                    ->delete();
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Cannot reliably reverse (v3 permissions may no longer be in DB after cleanup)
    }

    private function toV4Name(string $v3Name): ?string
    {
        foreach ($this->prefixMap as $prefix => $action) {
            if (str_starts_with($v3Name, $prefix)) {
                $resource = substr($v3Name, strlen($prefix));
                $model = $this->resourceMap[$resource] ?? null;
                if ($model) {
                    return $action . ':' . $model;
                }
                return null;
            }
        }
        return null;
    }
};
