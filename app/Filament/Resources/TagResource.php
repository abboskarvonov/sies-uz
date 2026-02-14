<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Teglar';

    protected static ?string $pluralModelLabel = 'Teglar';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Teg nomi'),
                Select::make('status')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('order')->numeric()->default(0),
                FileUpload::make('image')->image()->directory('tags'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('name')->label('Sarlavha')->searchable(),
                IconColumn::make('status')->boolean(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
