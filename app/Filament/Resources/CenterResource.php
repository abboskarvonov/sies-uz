<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\CenterResource\Pages\ViewCenter;
use App\Filament\Resources\CenterResource\Pages\EditCenter;
use App\Filament\Resources\CenterResource\Pages\ListCenters;
use App\Filament\Resources\CenterResource\Pages\CreateCenter;
use App\Filament\Actions\SyncPageContentBulkAction;
use App\Filament\Resources\CenterResource\Pages;

class CenterResource extends BasePageResource
{
    protected static array $pageTypes = ['center'];
    protected static string $permissionPrefix = 'center';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | \UnitEnum | null $navigationGroup = 'Tuzilmalar';

    protected static ?string $navigationLabel = 'Markazlar';

    protected static ?string $pluralModelLabel = 'Markazlar';

    protected static ?string $modelLabel = 'Markaz';

    protected static ?int $navigationSort = 3;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewCenter::class,
            EditCenter::class,
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
            'index'  => ListCenters::route('/'),
            'create' => CreateCenter::route('/create'),
            'edit'   => EditCenter::route('/{record}/edit'),
            'view'   => ViewCenter::route('/{record}'),
        ];
    }
}
