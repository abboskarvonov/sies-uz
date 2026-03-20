<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SymbolResource\Pages\ListSymbols;
use App\Filament\Resources\SymbolResource\Pages\CreateSymbol;
use App\Filament\Resources\SymbolResource\Pages\EditSymbol;
use App\Filament\Resources\SymbolResource\Pages;
use App\Models\Symbol;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;

class SymbolResource extends Resource
{
    protected static ?string $model = Symbol::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    protected static string | \UnitEnum | null $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $recordTitleAttribute = 'title_uz';

    protected static ?string $navigationLabel = 'Ramzlar';

    protected static ?string $pluralModelLabel = 'Ramzlar';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('O‘zbekiston Ramzlari')
                    ->schema([
                        Tabs::make('Tillar')
                            ->tabs([
                                Tab::make('O‘zbekcha')
                                    ->schema([
                                        TextInput::make('title_uz')->required(),
                                        TextInput::make('slug_uz')
                                            ->required()
                                            ->unique(ignorable: fn($record) => $record),
                                        TinyEditor::make('content_uz')->showMenuBar()->columnSpanFull(),
                                    ]),
                                Tab::make('Русский')
                                    ->schema([
                                        TextInput::make('title_ru'),
                                        TextInput::make('slug_ru')
                                            ->unique(ignorable: fn($record) => $record),
                                        TinyEditor::make('content_ru')->showMenuBar()->columnSpanFull(),
                                    ]),
                                Tab::make('English')
                                    ->schema([
                                        TextInput::make('title_en'),
                                        TextInput::make('slug_en')
                                            ->unique(ignorable: fn($record) => $record),
                                        TinyEditor::make('content_en')->showMenuBar()->columnSpanFull(),
                                    ]),


                            ]),
                        FileUpload::make('image')
                            ->label('Rasm')
                            ->directory('symbols')
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
                TextColumn::make('title_uz')->label("Sarlavha (UZ)")->searchable(),
                TextColumn::make('slug_uz')->label("Slug (UZ)"),
                TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSymbols::route('/'),
            'create' => CreateSymbol::route('/create'),
            'edit' => EditSymbol::route('/{record}/edit'),
        ];
    }
}
