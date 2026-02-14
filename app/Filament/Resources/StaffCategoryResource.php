<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffCategoryResource\Pages;
use App\Filament\Resources\StaffCategoryResource\RelationManagers;
use App\Models\Page;
use App\Models\StaffCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffCategoryResource extends Resource
{
    protected static ?string $model = StaffCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $recordTitleAttribute = 'title_uz';

    protected static ?string $navigationLabel = 'Xodimlar kategoriyalari';

    protected static ?string $pluralModelLabel = 'Xodimlar kategoriyalari';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return authUser()?->hasRole('super-admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sarlovhalar')
                    ->schema([
                        Forms\Components\TextInput::make('title_uz')
                            ->label('Nomi (UZ)')
                            ->required(),

                        Forms\Components\TextInput::make('title_ru')
                            ->label('Nomi (RU)')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('Nomi (EN)')
                            ->required(),
                    ])
                    ->columns(3),
                Section::make('Sahifa, tartiblanish va kategoriya')
                    ->schema([
                        Forms\Components\Select::make('page_id')
                            ->label('Tegishli sahifa (Fakultet/Kafedra/Markaz)')
                            ->options(function () {
                                return Page::whereIn('page_type', ['faculty', 'department', 'center', 'section'])
                                    ->pluck('title_uz', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),


                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'title_uz')
                            ->label('Yuqori kategoriya')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_uz')->label('UZ'),
                Tables\Columns\TextColumn::make('page.title_uz')->label('Sahifa'),
                Tables\Columns\TextColumn::make('parent.title_uz')->label('Yuqori kategoriya')->default('-'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffCategories::route('/'),
            'create' => Pages\CreateStaffCategory::route('/create'),
            'edit' => Pages\EditStaffCategory::route('/{record}/edit'),
        ];
    }
}