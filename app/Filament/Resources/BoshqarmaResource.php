<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\BoshqarmaResource\Pages\ViewBoshqarma;
use App\Filament\Resources\BoshqarmaResource\Pages\EditBoshqarma;
use App\Filament\Resources\BoshqarmaResource\Pages\ListBoshqarmalar;
use App\Filament\Resources\BoshqarmaResource\Pages\CreateBoshqarma;
use App\Filament\Resources\BoshqarmaResource\Pages;

class BoshqarmaResource extends BasePageResource
{
    protected static array $pageTypes = ['boshqarma'];
    protected static string $permissionPrefix = 'boshqarma';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Tuzilmalar';

    protected static ?string $navigationLabel = 'Boshqarmalar';

    protected static ?string $pluralModelLabel = 'Boshqarmalar';

    protected static ?string $modelLabel = 'Boshqarma';

    protected static ?int $navigationSort = 5;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewBoshqarma::class,
            EditBoshqarma::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBoshqarmalar::route('/'),
            'create' => CreateBoshqarma::route('/create'),
            'edit'   => EditBoshqarma::route('/{record}/edit'),
            'view'   => ViewBoshqarma::route('/{record}'),
        ];
    }
}
