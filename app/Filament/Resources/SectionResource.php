<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\SectionResource\Pages\ViewSection;
use App\Filament\Resources\SectionResource\Pages\EditSection;
use App\Filament\Resources\SectionResource\Pages\ListSections;
use App\Filament\Resources\SectionResource\Pages\CreateSection;
use App\Filament\Actions\SyncPageContentBulkAction;
use App\Filament\Resources\SectionResource\Pages;

class SectionResource extends BasePageResource
{
    protected static array $pageTypes = ['section'];

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Tuzilmalar';

    protected static ?string $navigationLabel = "Bo'limlar";

    protected static ?string $pluralModelLabel = "Bo'limlar";

    protected static ?string $modelLabel = "Bo'lim";

    protected static ?int $navigationSort = 4;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewSection::class,
            EditSection::class,
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
            'index'  => ListSections::route('/'),
            'create' => CreateSection::route('/create'),
            'edit'   => EditSection::route('/{record}/edit'),
            'view'   => ViewSection::route('/{record}'),
        ];
    }
}
