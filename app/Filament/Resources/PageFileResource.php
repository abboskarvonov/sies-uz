<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PageFileResource\Pages\ListPageFiles;
use App\Filament\Resources\PageFileResource\Pages\CreatePageFile;
use App\Filament\Resources\PageFileResource\Pages\EditPageFile;
use App\Filament\Resources\PageFileResource\Pages;
use App\Models\PageFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PageFileResource extends Resource
{
    protected static ?string $model = PageFile::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Sahifa fayllari';

    protected static ?string $pluralModelLabel = 'Sahifa fayllari';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page_id')
                    ->label('Sahifa')
                    ->relationship('page', 'title_uz')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Sahifani tanlang')
                    ->reactive(),

                TextInput::make('name')
                    ->label('Fayl nomi')
                    ->placeholder('Nom kiritilmasa fayl nomidan olinadi'),

                FileUpload::make('file')
                    ->label('Fayllar')
                    ->multiple()
                    ->disk('public')
                    ->directory('pages/files')
                    ->preserveFilenames()
                    ->saveUploadedFileUsing(function ($file) {
                        $directory = 'pages/files';
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();

                        $i = 1;
                        $fileName = $originalName . '.' . $extension;

                        while (Storage::disk('public')->exists($directory . '/' . $fileName)) {
                            $fileName = $originalName . '-' . $i . '.' . $extension;
                            $i++;
                        }

                        // Faylni saqlaymiz
                        $path = $file->storeAs($directory, $fileName, 'public');

                        return $path;
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('page.title_uz')
                    ->label('Sahifa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Fayl nomi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable(),
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
            'index' => ListPageFiles::route('/'),
            'create' => CreatePageFile::route('/create'),
            'edit' => EditPageFile::route('/{record}/edit'),
        ];
    }
}