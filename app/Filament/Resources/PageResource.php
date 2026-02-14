<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $recordTitleAttribute = 'title_uz';

    protected static ?string $navigationLabel = 'Sahifalar';

    protected static ?string $pluralModelLabel = 'Sahifalar';

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        // Adminlar hammasini ko‘ra oladi
        if (authUser()?->hasAnyRole(['super-admin', 'admin'])) {
            return $query;
        }

        // StaffMember bo‘lmasa, hech narsa ko‘rmasin:
        if (!$user->staffMember) {
            return $query->whereRaw('1=0');
        }

        // Pivot orqali bog‘langan Page ID larini filterlaymiz
        $pageIds = $user->staffMember
            ->pages()
            ->pluck('pages.id')
            ->toArray();

        return $query->whereIn('id', $pageIds);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sarlovha va kontentlar')
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Uz')->schema([
                                    TextInput::make('title_uz')->required(),
                                    TinyEditor::make('content_uz'),
                                ]),
                                Tabs\Tab::make('Ru')->schema([
                                    TextInput::make('title_ru')->nullable(),
                                    TinyEditor::make('content_ru'),
                                ]),
                                Tabs\Tab::make('En')->schema([
                                    TextInput::make('title_en')->nullable(),
                                    TinyEditor::make('content_en'),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Menyular')
                    ->schema([
                        Select::make('menu_id')
                            ->label('Menyu')
                            ->relationship(
                                name: 'menu',
                                titleAttribute: 'title_uz',
                                modifyQueryUsing: fn($query) => $query->orderBy('order')
                            )
                            ->reactive()
                            ->preload()
                            ->searchable(),

                        Select::make('submenu_id')
                            ->label('Submenyu')
                            ->options(fn($get) => $get('menu_id') ?
                                Submenu::where('menu_id', $get('menu_id'))->pluck('title_uz', 'id') : [])
                            ->reactive()
                            ->preload()
                            ->searchable(),

                        Select::make('multimenu_id')
                            ->label('Multimenu')
                            ->options(fn($get) => $get('submenu_id') ?
                                Multimenu::where('submenu_id', $get('submenu_id'))->pluck('title_uz', 'id') : [])
                            ->searchable()
                            ->preload(),
                    ])
                    ->collapsed(false)
                    ->columns(3)
                    ->visible(fn(): bool => authUser()?->hasRole('super-admin') || authUser()?->hasRole('admin')),

                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Sana')
                            ->required(),
                        Select::make('page_type')
                            ->options([
                                'default' => 'Oddiy',
                                'blog' => 'Blog',
                                'faculty' => 'Fakultet',
                                'department' => 'Kafedra',
                                'center' => 'Markaz',
                                'section' => 'Bo`lim',
                            ])
                            ->required()
                            ->reactive(),
                        Select::make('multimenus')
                            ->label('Bir nechta menyularni tanlash')
                            ->relationship('multimenus', 'title_uz')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn(Get $get) => $get('page_type') === 'blog'),
                        Select::make('status')
                            ->options([
                                'active' => 'Faol',
                                'inactive' => 'Nofaol',
                            ])
                            ->required(),

                        Select::make('tags')
                            ->label('Teglar')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        TextInput::make('order')->numeric()->default(0),
                        Toggle::make('activity')
                            ->label('Ilmiy faoliyat')
                            ->default(false),
                    ])
                    ->columns(3)
                    ->visible(fn(): bool => authUser()?->hasRole('super-admin') || authUser()?->hasRole('admin')),

                Section::make('Rasmlar')
                    ->schema([
                        FileUpload::make('image')->image()->directory('pages')->nullable(),
                        FileUpload::make('images')
                            ->label('Galereya')
                            ->multiple()
                            ->image()
                            ->directory('pages/gallery')
                            ->reorderable()
                            ->getUploadedFileNameForStorageUsing(fn($file) => $file->hashName())
                            ->formatStateUsing(fn($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                FilamentExportHeaderAction::make('Export')
            ])
            ->reorderable('order')
            ->defaultSort('order', 'desc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('title_uz')->label('Sarlavha')->searchable(),
                TextColumn::make('menu.title_uz')->label('Asosiy menyu'),
                TextColumn::make('submenu.title_uz')->label('Ichki menyu'),
                TextColumn::make('multimenu.title_uz')->label('Multi menyu')->searchable(),
                TextColumn::make('multimenus')
                    ->label('Multimenular')
                    ->getStateUsing(
                        fn($record) =>
                        $record->multimenus
                            ->pluck('title_' . app()->getLocale())
                            ->implode(', ')
                    )
                    ->wrap(),
                TextColumn::make('date')->date()->label('Sana')->sortable(
                    query: fn($query, $direction) =>
                    $query->orderByRaw("STR_TO_DATE(date, '%Y-%m-%d') {$direction}")
                ),
                TextColumn::make('page_type')->label('Sahifa turi'),
                IconColumn::make('status')->boolean(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('page_type')
                    ->label('Turi')
                    ->options([
                        'default' => 'Oddiy',
                        'blog' => 'Blog',
                        'faculty' => 'Fakultet',
                        'department' => 'Kafedra',
                        'center' => 'Markaz',
                        'section' => 'Bo`lim',
                    ]),
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
                    FilamentExportBulkAction::make('Export'),
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

                Components\Section::make('Kontentlar')
                    ->schema([
                        Components\TextEntry::make('content_uz')
                            ->label('Kontent (UZ)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                        Components\TextEntry::make('content_ru')
                            ->label('Kontent (RU)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                        Components\TextEntry::make('content_en')
                            ->label('Kontent (EN)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                    ])
                    ->columns(3),

                Components\Section::make('Bog\'langan menyular')
                    ->schema([
                        Components\TextEntry::make('menu.title_uz')->label('Menu nomi'),
                        Components\TextEntry::make('submenu.title_uz')->label('Submenu nomi'),
                        Components\TextEntry::make('multimenu.title_uz')->label('Multimenu nomi'),
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
                        Components\TextEntry::make('status')->label('Holati'),
                        Components\TextEntry::make('page_type')->label('Sahifa turi'),
                        Components\IconEntry::make('activity')
                            ->label('Ilmiy faoliyat')
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        Components\TextEntry::make('link')->label('Link'),
                        Components\TextEntry::make('order')->label('Tartib raqami'),
                        Components\RepeatableEntry::make('tags')
                            ->label('Teglar')
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label(false)
                                    ->badge()
                                    ->color('primary'),
                            ])
                            ->contained(false),
                    ])
                    ->columns(3),
                Components\Section::make('RasmLar')
                    ->schema([
                        Components\ImageEntry::make('image')->label('Rasm'),
                        Components\ImageEntry::make('images')
                            ->label('Qo\'shimcha rasmlar')
                            ->getStateUsing(function ($record) {
                                $images = is_string($record->images)
                                    ? json_decode($record->images, true)
                                    : ($record->images ?? []);

                                // Har bir rasmni to‘liq URL ga aylantiramiz
                                return collect($images)->map(fn($img) => asset(Storage::url($img)))->toArray();
                            })
                            ->extraImgAttributes([
                                'alt' => 'Logo',
                                'loading' => 'lazy',
                            ])
                            ->height(150)
                            ->ring(3)
                            ->limit(6)
                            ->limitedRemainingText()
                    ])
                    ->columns(2),

            ]);
    }

    public static function getRecordSubNavigation(Filament\Pages\Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPage::class,
            Pages\EditPage::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PageResource\RelationManagers\StaffCategoriesRelationManager::class,
            PageResource\RelationManagers\StaffMembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
            'view' => Pages\ViewPage::route('/{record}'),
        ];
    }
}
