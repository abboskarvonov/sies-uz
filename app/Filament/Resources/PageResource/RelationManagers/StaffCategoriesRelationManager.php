<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'staffCategories';

    protected static ?string $title = 'Xodim kategoriyalari';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->page_type, ['department', 'faculty', 'center', 'section']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Uz')->schema([
                            TextInput::make('title_uz')->label('Nomi (UZ)')->required(),
                        ]),
                        Tab::make('Ru')->schema([
                            TextInput::make('title_ru')->label('Nomi (RU)'),
                        ]),
                        Tab::make('En')->schema([
                            TextInput::make('title_en')->label('Nomi (EN)'),
                        ]),
                    ])
                    ->columnSpanFull(),

                Select::make('parent_id')
                    ->label('Ota kategoriya')
                    ->relationship(
                        'parent',
                        'title_uz',
                        fn($query) => $query->where('page_id', $this->getOwnerRecord()->id)
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('order')
                    ->label('Tartib')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('order')->label('#')->sortable(),
                TextColumn::make('title_uz')->label('Nomi (UZ)')->searchable(),
                TextColumn::make('title_ru')->label('Nomi (RU)'),
                TextColumn::make('title_en')->label('Nomi (EN)'),
                TextColumn::make('parent.title_uz')->label('Ota kategoriya'),
                TextColumn::make('employees_count')
                    ->label('Xodimlar soni')
                    ->counts('employees'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
