<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageFileResource\Pages;
use App\Models\PageFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PageFileResource extends Resource
{
    protected static ?string $model = PageFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Sahifa fayllari';

    protected static ?string $pluralModelLabel = 'Sahifa fayllari';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            'index' => Pages\ListPageFiles::route('/'),
            'create' => Pages\CreatePageFile::route('/create'),
            'edit' => Pages\EditPageFile::route('/{record}/edit'),
        ];
    }
}