<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\FacultyResource\Pages\ViewFaculty;
use App\Filament\Resources\FacultyResource\Pages\EditFaculty;
use App\Filament\Resources\FacultyResource\Pages\ListFaculties;
use App\Filament\Resources\FacultyResource\Pages\CreateFaculty;
use App\Filament\Actions\SyncPageContentBulkAction;
use App\Filament\Resources\FacultyResource\Pages;

class FacultyResource extends BasePageResource
{
    protected static array $pageTypes = ['faculty'];

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Tuzilmalar';

    protected static ?string $navigationLabel = 'Fakultetlar';

    protected static ?string $pluralModelLabel = 'Fakultetlar';

    protected static ?string $modelLabel = 'Fakultet';

    protected static ?int $navigationSort = 1;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewFaculty::class,
            EditFaculty::class,
        ];
    }

    protected static function getExtraBulkActions(): array
    {
        return [
            SyncPageContentBulkAction::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFaculties::route('/'),
            'create' => CreateFaculty::route('/create'),
            'edit'   => EditFaculty::route('/{record}/edit'),
            'view'   => ViewFaculty::route('/{record}'),
        ];
    }
}
