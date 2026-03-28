<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Role jadvaliga yozilmaydiganlari chiqarib tashlaymiz.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach (RoleResource::allResourceKeys() as $key) {
            unset($data["perm_{$key}"]);
        }

        return $data;
    }

    /**
     * Yaratishdan keyin: tanlangan permissionlarni rolga biriktirish.
     */
    protected function afterCreate(): void
    {
        /** @var Role $role */
        $role = $this->getRecord();
        $allSelected = [];

        foreach (RoleResource::TABS as $resources) {
            foreach ($resources as $key => $config) {
                $selected = $this->data["perm_{$key}"] ?? [];
                $valid = array_intersect((array) $selected, array_keys($config['perms']));
                $allSelected = array_merge($allSelected, array_values($valid));
            }
        }

        $role->syncPermissions($allSelected);
    }
}
