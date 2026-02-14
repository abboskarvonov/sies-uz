<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SymbolResource\Pages;
use App\Models\Symbol;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SymbolResource extends Resource
{
    protected static ?string $model = Symbol::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $recordTitleAttribute = 'title_uz';

    protected static ?string $navigationLabel = 'Ramzlar';

    protected static ?string $pluralModelLabel = 'Ramzlar';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('O‘zbekiston Ramzlari')
                    ->schema([
                        Tabs::make('Tillar')
                            ->tabs([
                                Tabs\Tab::make('O‘zbekcha')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_uz')->required(),
                                        Forms\Components\TextInput::make('slug_uz')
                                            ->required()
                                            ->unique(ignorable: fn($record) => $record),
                                        Forms\Components\RichEditor::make('content_uz'),
                                    ]),
                                Tabs\Tab::make('Русский')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ru'),
                                        Forms\Components\TextInput::make('slug_ru')
                                            ->unique(ignorable: fn($record) => $record),
                                        Forms\Components\RichEditor::make('content_ru'),
                                    ]),
                                Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en'),
                                        Forms\Components\TextInput::make('slug_en')
                                            ->unique(ignorable: fn($record) => $record),
                                        Forms\Components\RichEditor::make('content_en'),
                                    ]),


                            ]),
                        FileUpload::make('image')
                            ->label('Rasm')
                            ->directory('symbols')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_uz')->label("Sarlavha (UZ)")->searchable(),
                Tables\Columns\TextColumn::make('slug_uz')->label("Slug (UZ)"),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
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
            'index' => Pages\ListSymbols::route('/'),
            'create' => Pages\CreateSymbol::route('/create'),
            'edit' => Pages\EditSymbol::route('/{record}/edit'),
        ];
    }
}
