<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Helpers\SlugHelper;
use App\Models\Multimenu;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Submenu;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                        Select::make('submenu_id')
                            ->label('Ichki menyu')
                            ->options(function () {
                                $menuId = $this->getOwnerRecord()->getKey();
                                return Submenu::where('menu_id', $menuId)
                                    ->orderBy('order')
                                    ->pluck('title_uz', 'id');
                            })
                            ->searchable()
                            ->required(),
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
                    ->columns(2),

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
                TextColumn::make('submenu.title_uz')->label('Ichki menyu'),
                IconColumn::make('status')->boolean()->label('Holati'),
                TextColumn::make('order')->label('Tartib')->sortable(),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('submenu_id')
                    ->label('Ichki menyu')
                    ->options(function () {
                        $menuId = $this->getOwnerRecord()->getKey();
                        return Submenu::where('menu_id', $menuId)
                            ->orderBy('order')
                            ->pluck('title_uz', 'id');
                    }),
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
                EditAction::make()
                    ->mutateDataUsing(function (array $data, Multimenu $record): array {
                        $data['slug_uz'] = SlugHelper::generateUniqueSlug(Multimenu::class, 'slug_uz', $data['title_uz'], $record->id);
                        $data['slug_ru'] = SlugHelper::generateUniqueSlug(Multimenu::class, 'slug_ru', $data['title_ru'], $record->id);
                        $data['slug_en'] = SlugHelper::generateUniqueSlug(Multimenu::class, 'slug_en', $data['title_en'], $record->id);
                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
