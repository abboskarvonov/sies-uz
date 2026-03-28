<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use App\Filament\Resources\DepartmentResource\Pages\ViewDepartment;
use App\Filament\Resources\DepartmentResource\Pages\EditDepartment;
use App\Filament\Resources\DepartmentResource\Pages\ListDepartments;
use App\Filament\Resources\DepartmentResource\Pages\CreateDepartment;
use App\Filament\Actions\SyncPageContentBulkAction;
use App\Filament\Resources\DepartmentResource\Pages;

class DepartmentResource extends BasePageResource
{
    protected static array $pageTypes = ['department'];
    protected static string $permissionPrefix = 'department';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';

    protected static string | \UnitEnum | null $navigationGroup = 'Tuzilmalar';

    protected static ?string $navigationLabel = 'Kafedralar';

    protected static ?string $pluralModelLabel = 'Kafedralar';

    protected static ?string $modelLabel = 'Kafedra';

    protected static ?int $navigationSort = 2;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static function getSubNavPages(): array
    {
        return [
            ViewDepartment::class,
            EditDepartment::class,
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
            'index'  => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit'   => EditDepartment::route('/{record}/edit'),
            'view'   => ViewDepartment::route('/{record}'),
        ];
    }
}
