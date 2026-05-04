<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Filament\Resources\SubmenuResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmenusRelationManager extends RelationManager
{
    protected static string $relationship = 'submenus';

    protected static ?string $title = 'Ichki menyular';

    protected static ?string $modelLabel = 'Ichki menyu';

    protected static ?string $pluralModelLabel = 'Ichki menyular';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        SpatieMediaLibraryFileUpload::make('image')->collection('image'),
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
                SpatieMediaLibraryImageColumn::make('image')->collection('image')->conversion('thumb')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Turi')
                    ->options([
                        'default' => 'Default',
                        'multimenu' => 'Multimenu',
                    ]),
                SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('view_multimenus')
                    ->label('Multi menyular')
                    ->icon('heroicon-o-queue-list')
                    ->color('info')
                    ->url(fn ($record) => SubmenuResource::getUrl('view', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
