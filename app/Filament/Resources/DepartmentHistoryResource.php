<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentHistoryResource\Pages;
use App\Filament\Resources\DepartmentHistoryResource\RelationManagers;
use App\Models\DepartmentHistory;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentHistoryResource extends Resource
{
    protected static ?string $model = DepartmentHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Kafedralar tarixi';

    protected static ?string $pluralModelLabel = 'Kafedralar tarixi';

    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('department_id')
                    ->label('Kafedra (Department)')
                    ->options(
                        Page::where('page_type', 'department')
                            ->pluck('title_uz', 'id')
                    )
                    ->required()
                    ->searchable()
                    ->preload(),

                RichEditor::make('content_uz')->label('Content (UZ)')->required(),
                RichEditor::make('content_ru')->label('Content (RU)')->required(),
                RichEditor::make('content_en')->label('Content (EN)')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('department.title_uz')->label('Kafedra'),
                TextColumn::make('createdBy.name')->label('Yaratgan foydalanuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartirgan foydalanuvchi'),
                TextColumn::make('created_at')->label('Yaratilgan vaqt')->dateTime('d.m.Y H:i')->sortable(),
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
            'index' => Pages\ListDepartmentHistories::route('/'),
            'create' => Pages\CreateDepartmentHistory::route('/create'),
            'edit' => Pages\EditDepartmentHistory::route('/{record}/edit'),
        ];
    }
}
