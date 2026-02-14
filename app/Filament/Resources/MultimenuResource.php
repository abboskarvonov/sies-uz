<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MultimenuResource\Pages;
use App\Models\Multimenu;
use App\Models\Submenu;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MultimenuResource extends Resource
{
    protected static ?string $model = Multimenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Menyular';

    protected static ?string $navigationLabel = 'Multi menyular';

    protected static ?string $pluralModelLabel = 'Multi menyular';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return authUser()?->hasRole('super-admin');
    }

    public static function form(Form $form): Form
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
                        Select::make('menu_id')
                            ->label('Asosiy menyu')
                            ->relationship(
                                name: 'menu',
                                titleAttribute: 'title_uz',
                                modifyQueryUsing: fn($query) => $query->orderBy('order')
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required(),

                        Select::make('submenu_id')
                            ->label('Submenyu')
                            ->options(function (callable $get) {
                                $menuId = $get('menu_id');

                                if (!$menuId) {
                                    return [];
                                }

                                return Submenu::where('menu_id', $menuId)
                                    ->orderBy('order')
                                    ->pluck('title_uz', 'id');
                            })
                            ->searchable()
                            ->preload()
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
                    ->columns(3),

                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')->image()->directory('multimenus')->nullable(),
                    ])
                    ->columns(1)
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('title_uz')->label('Sarlavha')->searchable(),
                TextColumn::make('menu.title_uz')->label('Menyu'),
                TextColumn::make('submenu.title_uz')->label('Submenyu'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Sarlovhalar')
                    ->schema([
                        Components\TextEntry::make('title_uz'),
                        Components\TextEntry::make('title_ru'),
                        Components\TextEntry::make('title_en'),
                    ])
                    ->columns(3),

                Components\Section::make('Sluglar')
                    ->schema([
                        Components\TextEntry::make('slug_uz'),
                        Components\TextEntry::make('slug_ru'),
                        Components\TextEntry::make('slug_en'),
                    ])
                    ->columns(3),

                Components\Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        Components\TextEntry::make('menu.title_uz')->label('Asosiy menyu'),
                        Components\TextEntry::make('submenu.title_uz')->label('Submenyu'),
                        Components\TextEntry::make('status')->label('Holati'),
                        Components\TextEntry::make('link')->label('Link'),
                        Components\TextEntry::make('order')->label('Tartib raqami'),
                    ])
                    ->columns(3),
                Components\Section::make('Rasm')
                    ->schema([
                        Components\ImageEntry::make('image')->label('Rasm'),
                    ])
                    ->columns(1),


            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewMultimenu::class,
            Pages\EditMultimenu::class,
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
            'index' => Pages\ListMultimenus::route('/'),
            'create' => Pages\CreateMultimenu::route('/create'),
            'edit' => Pages\EditMultimenu::route('/{record}/edit'),
            'view' => Pages\ViewMultimenu::route('/{record}'),
        ];
    }
}