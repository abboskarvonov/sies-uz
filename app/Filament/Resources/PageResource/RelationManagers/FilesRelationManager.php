<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    protected static ?string $title = 'Fayllar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Fayl nomi')
                    ->placeholder('Nom kiritilmasa fayl nomidan olinadi'),

                FileUpload::make('file')
                    ->label('Fayl')
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

                        return $file->storeAs($directory, $fileName, 'public');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')->label('Fayl nomi')->searchable(),
                TextColumn::make('file')->label('Fayl'),
                TextColumn::make('created_at')->label('Yaratilgan')->dateTime('d.m.Y H:i'),
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
