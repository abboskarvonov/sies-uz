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
use App\Filament\Resources\SubmenuResource\Pages\ViewSubmenu;
use App\Filament\Resources\SubmenuResource\Pages\EditSubmenu;
use App\Filament\Resources\SubmenuResource\RelationManagers\MultimenusRelationManager;
use App\Filament\Resources\SubmenuResource\Pages\ListSubmenus;
use App\Filament\Resources\SubmenuResource\Pages\CreateSubmenu;
use App\Filament\Resources\SubmenuResource\Pages;
use App\Models\Submenu;
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

class SubmenuResource extends Resource
{
    protected static ?string $model = Submenu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Menyular';

    protected static ?string $navigationLabel = 'Ichki menyular';

    protected static ?string $pluralModelLabel = 'Ichki menyular';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $shouldRegisterNavigation = false;

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
                        Select::make('menu_id')
                            ->label('Asosiy menyu')
                            ->relationship(
                                name: 'menu',
                                titleAttribute: 'title_uz',
                                modifyQueryUsing: fn($query) => $query->orderBy('order')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('type')
                            ->options([
                                'default' => 'Default',
                                'multimenu' => 'Multimenu',
                            ])
                            ->required(),

                        Select::make('status')
                            ->options([
                                'active' => 'Faol',
                                'inactive' => 'Nofaol',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('link')->url()->nullable(),
                        TextInput::make('order')->numeric()->default(0),
                    ])
                    ->columns(3),
                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')->directory('submenus'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('title_uz')->label('Sarlavha')->searchable(),
                TextColumn::make('menu.title_uz')->label('Asosiy menyu'),
                TextColumn::make('type')->sortable(),
                IconColumn::make('status')->boolean(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Turi')
                    ->options([
                        'default' => 'Default',
                        'multimenu' => 'Multimenu',
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
                        TextEntry::make('menu.title_uz')->label('Asosiy menyu'),
                        TextEntry::make('status')->label('Holati'),
                        TextEntry::make('type')->label('Turi'),
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
            ViewSubmenu::class,
            EditSubmenu::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            MultimenusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubmenus::route('/'),
            'create' => CreateSubmenu::route('/create'),
            'edit' => EditSubmenu::route('/{record}/edit'),
            'view' => ViewSubmenu::route('/{record}'),
        ];
    }
}