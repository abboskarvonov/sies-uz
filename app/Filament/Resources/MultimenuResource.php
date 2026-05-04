<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use App\Filament\Resources\MultimenuResource\Pages\ViewMultimenu;
use App\Filament\Resources\MultimenuResource\Pages\EditMultimenu;
use App\Filament\Resources\MultimenuResource\Pages\ListMultimenus;
use App\Filament\Resources\MultimenuResource\Pages\CreateMultimenu;
use App\Filament\Resources\MultimenuResource\Pages;
use App\Models\Multimenu;
use App\Models\Submenu;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components;
use Filament\Pages\Page;
use App\Filament\Concerns\HasSpatiePermissions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MultimenuResource extends Resource
{
    use HasSpatiePermissions;

    protected static string $permPrefix = 'Multimenu';

    protected static ?string $model = Multimenu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Menyular';

    protected static ?string $navigationLabel = 'Multi menyular';

    protected static ?string $pluralModelLabel = 'Multi menyular';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
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
                        SpatieMediaLibraryFileUpload::make('image')->collection('image'),
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
                SpatieMediaLibraryImageColumn::make('image')->collection('image')->conversion('thumb')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sarlovhalar')
                    ->schema([
                        TextEntry::make('title_uz'),
                        TextEntry::make('title_ru'),
                        TextEntry::make('title_en'),
                    ])
                    ->columns(3),

                Section::make('Sluglar')
                    ->schema([
                        TextEntry::make('slug_uz'),
                        TextEntry::make('slug_ru'),
                        TextEntry::make('slug_en'),
                    ])
                    ->columns(3),

                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        TextEntry::make('menu.title_uz')->label('Asosiy menyu'),
                        TextEntry::make('submenu.title_uz')->label('Submenyu'),
                        TextEntry::make('status')->label('Holati'),
                        TextEntry::make('link')->label('Link'),
                        TextEntry::make('order')->label('Tartib raqami'),
                    ])
                    ->columns(3),
                Section::make('Rasm')
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('image')->collection('image')->conversion('webp')->label('Rasm'),
                    ])
                    ->columns(1),


            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewMultimenu::class,
            EditMultimenu::class,
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
            'index' => ListMultimenus::route('/'),
            'create' => CreateMultimenu::route('/create'),
            'edit' => EditMultimenu::route('/{record}/edit'),
            'view' => ViewMultimenu::route('/{record}'),
        ];
    }
}