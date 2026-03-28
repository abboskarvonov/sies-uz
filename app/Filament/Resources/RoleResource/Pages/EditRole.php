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
     * Formga yuklashdan oldin: rolning permissionlarini
     * perm_{key} fieldlarga ajratib to'ldirish.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Role $role */
        $role = $this->getRecord();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        foreach (RoleResource::TABS as $resources) {
            foreach ($resources as $key => $config) {
                $data["perm_{$key}"] = array_values(
                    array_intersect($rolePermissions, array_keys($config['perms']))
                );
            }
        }

        return $data;
    }

    /**
     * Modelga yozilmaydiganlari chiqarib tashlaymiz —
     * perm_{key} fieldlar faqat afterSave da ishlatiladi.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (RoleResource::allResourceKeys() as $key) {
            unset($data["perm_{$key}"]);
        }

        return $data;
    }

    /**
     * Saqlashdan keyin: barcha tanlangan permissionlarni sync qilish.
     */
    protected function afterSave(): void
    {
        $this->syncRolePermissions($this->getRecord());
    }

    private function syncRolePermissions(Role $role): void
    {
        $allSelected = [];

        foreach (RoleResource::TABS as $resources) {
            foreach ($resources as $key => $config) {
                $selected = $this->data["perm_{$key}"] ?? [];
                // Faqat bizning permission ro'yxatida borlarini qabul qilamiz
                $valid = array_intersect((array) $selected, array_keys($config['perms']));
                $allSelected = array_merge($allSelected, array_values($valid));
            }
        }

        $role->syncPermissions($allSelected);
    }
}
