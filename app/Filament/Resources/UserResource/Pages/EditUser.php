<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $emailVerified = $this->data['email_verified'] ?? null;

        if ($emailVerified && !$this->record->email_verified_at) {
            $this->record->update(['email_verified_at' => now()]);
        } elseif (!$emailVerified && $this->record->email_verified_at) {
            $this->record->update(['email_verified_at' => null]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
