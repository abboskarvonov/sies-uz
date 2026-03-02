<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

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

class SubmenusRelationManager extends RelationManager
{
    protected static string $relationship = 'submenus';

    protected static ?string $title = 'Ichki menyular';

    protected static ?string $modelLabel = 'Ichki menyu';

    protected static ?string $pluralModelLabel = 'Ichki menyular';

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
                        Select::make('type')
                            ->options([
                                'default' => 'Default',
                                'multimenu' => 'Multimenu',
                            ])
                            ->required(),
                        Select::make('status')
                            ->options([
                                'active' => 'Faol',
                                'inactive' => 'Nofaol',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('link')->url()->nullable(),
                        TextInput::make('order')->numeric()->default(0),
                    ])
                    ->columns(2),

                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')->directory('submenus'),
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
                TextColumn::make('type')->label('Turi'),
                IconColumn::make('status')->boolean()->label('Holati'),
                TextColumn::make('order')->label('Tartib')->sortable(),
                TextColumn::make('multimenus_count')
                    ->counts('multimenus')
                    ->label('Multi menyular')
                    ->badge()
                    ->color('info'),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Turi')
                    ->options([
                        'default' => 'Default',
                        'multimenu' => 'Multimenu',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_multimenus')
                    ->label('Multi menyular')
                    ->icon('heroicon-o-queue-list')
                    ->color('info')
                    ->url(fn ($record) => \App\Filament\Resources\SubmenuResource::getUrl('view', ['record' => $record])),
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
