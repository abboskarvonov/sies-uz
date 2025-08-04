<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Menyular';

    protected static ?string $navigationLabel = 'Menyular';

    protected static ?string $pluralModelLabel = 'Menyular';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        FileUpload::make('image')->image()->directory('menus')->nullable(),
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

                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                ImageColumn::make('image')->label('Rasm'),
            ])->defaultSort(('order'))
            ->filters([
                SelectFilter::make('position')
                    ->label('Position')
                    ->options([
                        'header' => 'Header',
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Sarlovhalar')
                    ->schema([
                        Components\TextEntry::make('title_uz'),
                        Components\TextEntry::make('title_ru'),
                        Components\TextEntry::make('title_en'),
                    ])
                    ->columns(3),

                Components\Section::make('Sluglar')
                    ->schema([
                        Components\TextEntry::make('slug_uz'),
                        Components\TextEntry::make('slug_ru'),
                        Components\TextEntry::make('slug_en'),
                    ])
                    ->columns(3),

                Components\Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        Components\TextEntry::make('status')->label('Holati'),
                        Components\TextEntry::make('menu_type'),
                        Components\TextEntry::make('position')->label('Pozitsiya'),
                        Components\TextEntry::make('link')->label('Link'),
                        Components\TextEntry::make('order')->label('Tartib raqami'),
                    ])
                    ->columns(3),
                Components\Section::make('Rasm')
                    ->schema([
                        Components\ImageEntry::make('image')->label('Rasm'),
                    ])
                    ->columns(1),


            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewMenu::class,
            Pages\EditMenu::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
            'view' => Pages\ViewMenu::route('/{record}'),
        ];
    }
}
