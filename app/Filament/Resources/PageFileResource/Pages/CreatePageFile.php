<?php

namespace App\Filament\Resources\PageFileResource\Pages;

use App\Filament\Resources\PageFileResource;
use App\Models\PageFile;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePageFile extends CreateRecord
{
    protected static string $resource = PageFileResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $files = $data['file'] ?? [];
        $lastCreated = null;

        foreach ($files as $filePath) {
            $data['file'] = $filePath;
            $data['name'] = basename($filePath);
            $lastCreated = PageFile::create($data);
        }

        return $lastCreated;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
