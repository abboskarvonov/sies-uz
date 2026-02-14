<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Uz')->schema([
                            TextInput::make('title_uz')->label('Nomi (UZ)')->required(),
                        ]),
                        Tabs\Tab::make('Ru')->schema([
                            TextInput::make('title_ru')->label('Nomi (RU)'),
                        ]),
                        Tabs\Tab::make('En')->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_uz')->label('Nomi (UZ)')->searchable(),
                TextColumn::make('title_ru')->label('Nomi (RU)'),
                TextColumn::make('title_en')->label('Nomi (EN)'),
                TextColumn::make('parent.title_uz')->label('Ota kategoriya'),
                TextColumn::make('staff_members_count')
                    ->label('Xodimlar soni')
                    ->counts('staffMembers'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
