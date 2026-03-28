<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Yaratishdan keyin: tanlangan permissionlarni rolga biriktirish.
     */
    protected function afterCreate(): void
    {
        /** @var Role $role */
        $role = $this->getRecord();
        $allSelected = [];

        foreach (RoleResource::PERMISSION_GROUPS as $key => $group) {
            $selected = $this->data["perm_{$key}"] ?? [];
            $allSelected = array_merge($allSelected, $selected);
        }

        $role->syncPermissions($allSelected);
    }
}
