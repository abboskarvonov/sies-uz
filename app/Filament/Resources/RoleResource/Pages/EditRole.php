<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    /**
     * Formni to'ldirishdan oldin: rol permissionlarini
     * har bir group uchun alohida perm_{key} fieldlarga ajratib yuklash.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Role $role */
        $role = $this->getRecord();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        foreach (RoleResource::PERMISSION_GROUPS as $key => $group) {
            $data["perm_{$key}"] = array_values(
                array_intersect($rolePermissions, array_keys($group['perms']))
            );
        }

        return $data;
    }

    /**
     * Saqlashdan keyin: barcha perm_{key} fieldlarni birlashtirb
     * rol permissionlarini sync qilish.
     */
    protected function afterSave(): void
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
