<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\PageResource\RelationManagers\FilesRelationManager;
use App\Filament\Resources\PageResource\RelationManagers\StaffCategoriesRelationManager;
use App\Filament\Resources\PageResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\PageResource\RelationManagers\DepartmentHistoryRelationManager;
use App\Models\User;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

abstract class BasePageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    protected static ?string $recordTitleAttribute = 'title_uz';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    /**
     * Filter this resource to these page_type values.
     * Empty array = no filter (show all types).
     * Single value = specialized resource (page_type hidden in form).
     */
    protected static array $pageTypes = [];

    /**
     * Spatie permission prefix for this resource.
     * Permissions: {prefix}.viewAny, {prefix}.create, {prefix}.update,
     *              {prefix}.delete, {prefix}.deleteAny
     *
     * Each child resource overrides this to get independent permissions.
     */
    protected static string $permissionPrefix = 'page';

    // ─── Authorization ────────────────────────────────────────────────

    public static function canViewAny(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        if ($user->can('manage_own_assigned_pages')) return true;
        return $user->can(static::$permissionPrefix . '.viewAny');
    }

    public static function canCreate(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can(static::$permissionPrefix . '.create');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return $user->hasAccessToPage($record);
        if ($user->can('manage_own_assigned_pages') && $user->hasAccessToPage($record)) return true;
        return $user->can(static::$permissionPrefix . '.update')
            && $user->hasAccessToPage($record);
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return $user->hasAccessToPage($record);
        if ($user->can('manage_own_assigned_pages') && $user->hasAccessToPage($record)) return true;
        return $user->can(static::$permissionPrefix . '.viewAny')
            && $user->hasAccessToPage($record);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can(static::$permissionPrefix . '.delete')
            && $user->hasAccessToPage($record);
    }

    public static function canDeleteAny(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can(static::$permissionPrefix . '.deleteAny');
    }

    protected static function isSingleType(): bool
    {
        return count(static::$pageTypes) === 1;
    }

    protected static function getPageTypeOptions(): array
    {
        $all = [
            'default'    => 'Oddiy',
            'blog'       => 'Blog',
            'faculty'    => 'Fakultet',
            'department' => 'Kafedra',
            'center'     => 'Markaz',
            'section'    => "Bo'lim",
            'boshqarma'  => 'Boshqarma',
        ];

        if (empty(static::$pageTypes)) {
            return $all;
        }

        return array_filter($all, fn($k) => in_array($k, static::$pageTypes), ARRAY_FILTER_USE_KEY);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!empty(static::$pageTypes)) {
            $query->whereIn('page_type', static::$pageTypes);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return $query;
        }

        if ($user->can('view_all_pages')) {
            return $query;
        }

        $pageIds = $user->assignedPages()->pluck('pages.id')->toArray();

        // manage_own_assigned_pages uchun pagePositions ham qo'shiladi
        if ($user->can('manage_own_assigned_pages')) {
            $positionPageIds = $user->pagePositions()->pluck('page_id')->toArray();
            $pageIds = array_unique(array_merge($pageIds, $positionPageIds));
        }

        if ($user->can('view_blog_pages') && in_array('blog', static::$pageTypes)) {
            return $query->where(function ($q) use ($pageIds) {
                $q->whereIn('id', $pageIds)
                  ->orWhere('page_type', 'blog');
            });
        }

        if (empty($pageIds)) {
            return $query->whereRaw('1=0');
        }

        return $query->whereIn('id', $pageIds);
    }

    public static function form(Schema $schema): Schema
    {
        $pageTypeField = static::isSingleType()
            ? Hidden::make('page_type')->default(static::$pageTypes[0])
            : Select::make('page_type')
                ->options(static::getPageTypeOptions())
                ->required()
                ->reactive();

        return $schema
            ->components([
                Section::make('Sarlovha va kontentlar')
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make('Uz')->schema([
                                    TextInput::make('title_uz')->required(),
                                    TinyEditor::make('content_uz')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tab::make('Ru')->schema([
                                    TextInput::make('title_ru')->nullable(),
                                    TinyEditor::make('content_ru')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tab::make('En')->schema([
                                    TextInput::make('title_en')->nullable(),
                                    TinyEditor::make('content_en')->showMenuBar()->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

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
                    ->columnSpanFull()
                    ->visible(fn(): bool => authUser()?->hasRole('super-admin') || authUser()?->hasRole('admin')),

                Section::make("Qo'shimcha ma'lumotlar")
                    ->schema([
                        DatePicker::make('date')
                            ->label('Sana')
                            ->required(),
                        $pageTypeField,
                        Select::make('parent_page_id')
                            ->label("Ota sahifa (Markaz / Fakultet)")
                            ->relationship(
                                name: 'parentPage',
                                titleAttribute: 'title_uz',
                                modifyQueryUsing: fn($query) => $query->whereIn('page_type', ['center', 'faculty'])
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText("Bo'lim qaysi markazga yoki kafedraga tegishli ekanini belgilaydi. HEMIS sync orqali avtomatik to'ldiriladi.")
                            ->visible(fn(): bool => in_array(static::$pageTypes[0] ?? '', ['section', 'department', 'boshqarma'])),
                        Select::make('multimenus')
                            ->label('Bir nechta menyularni tanlash')
                            ->relationship('multimenus', 'title_uz')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn(Get $get) => $get('page_type') === 'blog'),
                        Select::make('status')
                            ->options([
                                'active'   => 'Faol',
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
                    ->columnSpanFull()
                    ->visible(fn(): bool => authUser()?->hasRole('super-admin') || authUser()?->hasRole('admin')),

                Section::make('Rasmlar')
                    ->schema([
                        FileUpload::make('image')->directory('pages')->nullable(),
                        FileUpload::make('images')
                            ->label('Galereya')
                            ->multiple()
                            ->directory('pages/gallery')
                            ->reorderable()
                            ->getUploadedFileNameForStorageUsing(fn($file) => $file->hashName())
                            ->formatStateUsing(fn($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                FilamentExportHeaderAction::make('Export'),
            ])
            ->reorderable('order')
            ->defaultSort('order', 'desc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('title_uz')->label('Sarlavha')->searchable(),
                IconColumn::make('hemis_id')
                    ->label('HEMIS')
                    ->boolean()
                    ->getStateUsing(fn($record) => (bool) $record->hemis_id)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn($record) => $record->hemis_id ? 'HEMIS ID: ' . $record->hemis_id : 'Qo\'lda kiritilgan'),
                TextColumn::make('parentPage.title_uz')
                    ->label('Ota sahifa (Markaz/Fakultet)')
                    ->searchable()
                    ->toggleable(),
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
                TextColumn::make('page_type')
                    ->label('Sahifa turi')
                    ->hidden(static::isSingleType()),
                IconColumn::make('status')->boolean(),
                TextColumn::make('order')->sortable(),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label("O'zgartiruvchi"),
                ImageColumn::make('image')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('page_type')
                    ->label('Turi')
                    ->options(static::getPageTypeOptions())
                    ->hidden(static::isSingleType()),
                SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'active'   => 'Faol',
                        'inactive' => 'Nofaol',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make(array_merge(
                    [
                        DeleteBulkAction::make(),
                        FilamentExportBulkAction::make('Export'),
                    ],
                    static::getExtraBulkActions()
                )),
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

                Section::make('Kontentlar')
                    ->schema([
                        TextEntry::make('content_uz')
                            ->label('Kontent (UZ)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                        TextEntry::make('content_ru')
                            ->label('Kontent (RU)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                        TextEntry::make('content_en')
                            ->label('Kontent (EN)')
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->extraAttributes(['style' => 'max-height: 300px; overflow-y: auto; display: block;']),
                    ])
                    ->columns(3),

                Section::make("Bog'langan menyular")
                    ->schema([
                        TextEntry::make('menu.title_uz')->label('Menu nomi'),
                        TextEntry::make('submenu.title_uz')->label('Submenu nomi'),
                        TextEntry::make('multimenu.title_uz')->label('Multimenu nomi'),
                    ])
                    ->columns(3),

                Section::make('Sluglar')
                    ->schema([
                        TextEntry::make('slug_uz'),
                        TextEntry::make('slug_ru'),
                        TextEntry::make('slug_en'),
                    ])
                    ->columns(3),

                Section::make("Qo'shimcha ma'lumotlar")
                    ->schema([
                        TextEntry::make('status')->label('Holati'),
                        TextEntry::make('page_type')->label('Sahifa turi'),
                        IconEntry::make('activity')
                            ->label('Ilmiy faoliyat')
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('link')->label('Link'),
                        TextEntry::make('order')->label('Tartib raqami'),
                        RepeatableEntry::make('tags')
                            ->label('Teglar')
                            ->schema([
                                TextEntry::make('name')
                                    ->label(false)
                                    ->badge()
                                    ->color('primary'),
                            ])
                            ->contained(false),
                    ])
                    ->columns(3),

                Section::make('RasmLar')
                    ->schema([
                        ImageEntry::make('image')->label('Rasm'),
                        ImageEntry::make('images')
                            ->label("Qo'shimcha rasmlar")
                            ->getStateUsing(function ($record) {
                                $images = is_string($record->images)
                                    ? json_decode($record->images, true)
                                    : ($record->images ?? []);

                                return collect($images)->map(fn($img) => Storage::url($img))->toArray();
                            })
                            ->extraImgAttributes(['alt' => 'Logo', 'loading' => 'lazy'])
                            ->height(150)
                            ->ring(3)
                            ->limit(6)
                            ->limitedRemainingText(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FilesRelationManager::class,
            StaffCategoriesRelationManager::class,
            EmployeesRelationManager::class,
            DepartmentHistoryRelationManager::class,
        ];
    }

    /** Override in subclasses to list the View/Edit page classes for sub-navigation. */
    protected static function getSubNavPages(): array
    {
        return [];
    }

    public static function getRecordSubNavigation(Filament\Pages\Page $page): array
    {
        return $page->generateNavigationItems(static::getSubNavPages());
    }

    /** Override in subclasses to add extra bulk actions to the table. */
    protected static function getExtraBulkActions(): array
    {
        return [];
    }
}
