<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\MenuResource\Pages\ViewMenu;
use App\Filament\Resources\MenuResource\Pages\EditMenu;
use App\Filament\Resources\MenuResource\RelationManagers\SubmenusRelationManager;
use App\Filament\Resources\MenuResource\RelationManagers\MultimenusRelationManager;
use App\Filament\Resources\MenuResource\Pages\ListMenus;
use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Menyular';

    protected static ?string $navigationLabel = 'Menyular';

    protected static ?string $pluralModelLabel = 'Menyular';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sarlovhalar')
                    ->schema([
                        TextInput::make('title_uz')->required(),
                        TextInput::make('title_ru')->required(),
                        TextInput::make('title_en')->required(),
                    ])
                    ->columns(3),
                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        Select::make('menu_type')
                            ->options([
                                'default' => 'Default',
                                'dropdown' => 'Dropdown',
                            ])->required(),
                        Select::make('status')
                            ->options([
                                'active' => 'Faol',
                                'inactive' => 'Nofaol',
                            ])
                            ->default('active')
                            ->required()
                            ->label('Holati'),
                        Select::make('position')
                            ->options([
                                'header' => 'Header',
                                'quick_links' => 'Quick Links',
                                'footer' => 'Footer',
                            ])->required(),
                        Section::make()
                            ->schema([
                                TextInput::make('link')->nullable(),
                                TextInput::make('order')->numeric()->default(0)
                            ])->columns(2)
                    ])
                    ->columns(3),
                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')->directory('menus')->nullable(),
                    ])
                    ->columns(1)
                    ->collapsed(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('title_uz')->label('Sarlavha (UZ)')->searchable(),
                TextColumn::make('menu_type')->label('Type'),
                TextColumn::make('position')->label('Position'),
                TextColumn::make('order')->label('Order'),
                IconColumn::make('status')->boolean(),

                TextColumn::make('submenus_count')
                    ->counts('submenus')
                    ->label('Ichki menyular')
                    ->badge()
                    ->color('info'),
                TextColumn::make('multimenus_count')
                    ->counts('multimenus')
                    ->label('Multi menyular')
                    ->badge()
                    ->color('warning'),
                ImageColumn::make('image')->label('Rasm'),
            ])->defaultSort(('order'))
            ->filters([
                SelectFilter::make('position')
                    ->label('Position')
                    ->options([
                        'header' => 'Header',
                        'quick_links' => 'Quick Links',
                        'footer' => 'Footer',
                    ]),
                SelectFilter::make('menu_type')
                    ->label('Turi')
                    ->options([
                        'default' => 'Default',
                        'dropdown' => 'Dropdown',
                    ]),
                SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sarlovhalar')
                    ->schema([
                        TextEntry::make('title_uz'),
                        TextEntry::make('title_ru'),
                        TextEntry::make('title_en'),
                    ])
                    ->columns(3),

                Section::make('Sluglar')
                    ->schema([
                        TextEntry::make('slug_uz'),
                        TextEntry::make('slug_ru'),
                        TextEntry::make('slug_en'),
                    ])
                    ->columns(3),

                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        TextEntry::make('status')->label('Holati'),
                        TextEntry::make('menu_type'),
                        TextEntry::make('position')->label('Pozitsiya'),
                        TextEntry::make('link')->label('Link'),
                        TextEntry::make('order')->label('Tartib raqami'),
                    ])
                    ->columns(3),
                Section::make('Rasm')
                    ->schema([
                        ImageEntry::make('image')->label('Rasm'),
                    ])
                    ->columns(1),


            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewMenu::class,
            EditMenu::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            SubmenusRelationManager::class,
            MultimenusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
            'view' => ViewMenu::route('/{record}'),
        ];
    }
}