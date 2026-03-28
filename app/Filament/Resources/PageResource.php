<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\PageResource\Pages\ViewPage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages;

class PageResource extends BasePageResource
{
    protected static array $pageTypes = ['default', 'blog'];
    protected static string $permissionPrefix = 'page';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    protected static string | \UnitEnum | null $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Sahifalar';

    protected static ?string $pluralModelLabel = 'Sahifalar';

    protected static ?int $navigationSort = 2;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewPage::class,
            EditPage::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit'   => EditPage::route('/{record}/edit'),
            'view'   => ViewPage::route('/{record}'),
        ];
    }
}
