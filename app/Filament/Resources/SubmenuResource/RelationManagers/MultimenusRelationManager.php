<?php

namespace App\Filament\Resources\SubmenuResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MultimenusRelationManager extends RelationManager
{
    protected static string $relationship = 'multimenus';

    protected static ?string $title = 'Multi menyular';

    protected static ?string $modelLabel = 'Multi menyu';

    protected static ?string $pluralModelLabel = 'Multi menyular';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sarlovhalar')
                    ->schema([
                        TextInput::make('title_uz')->required(),
                        TextInput::make('title_ru')->required(),
                        TextInput::make('title_en')->required(),
                    ])
                    ->columns(3),

                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Faol',
                                'inactive' => 'Nofaol',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('link')->nullable(),
                        TextInput::make('order')->numeric()->default(0),
                    ])
                    ->columns(3),

                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')->directory('multimenus')->nullable(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('title_uz')->label('Sarlavha')->searchable(),
                IconColumn::make('status')->boolean()->label('Holati'),
                TextColumn::make('order')->label('Tartib')->sortable(),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['menu_id'] = $this->getOwnerRecord()->menu_id;
                        return $data;
                    }),
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
