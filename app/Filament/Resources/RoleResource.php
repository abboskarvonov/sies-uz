<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as BaseRoleResource;
use App\Filament\Resources\RoleResource\Pages;

class RoleResource extends BaseRoleResource
{
    /**
     * Fix: Shield's once() caches the first resource's permissions for ALL resources
     * because once() keys by file+line for static calls. Removing once() is safe
     * because getResources() itself is already cached via once() on the singleton.
     */
    public static function getResourcePermissionOptions(array $entity): array
    {
        return FilamentShield::getResourcePermissionsWithLabels($entity['resourceFqcn']) ?? [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view'   => Pages\ViewRole::route('/{record}'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
