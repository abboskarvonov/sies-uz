<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\StaffMemberResource\Pages\ListStaffMembers;
use App\Filament\Resources\StaffMemberResource\Pages\CreateStaffMember;
use App\Filament\Resources\StaffMemberResource\Pages\EditStaffMember;
use App\Models\User;
use App\Filament\Resources\StaffMemberResource\Pages;
use App\Models\Page;
use App\Models\StaffCategory;
use App\Models\StaffMember;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StaffMemberResource extends Resource
{
    protected static ?string $model = StaffMember::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Xodimlar';

    protected static ?string $pluralModelLabel = 'Xodimlar';

    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sarlovha va kontentlar')
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make('Uz')->schema([
                                    TextInput::make('name_uz')->label('Ism (UZ)')->required(),
                                    TextInput::make('position_uz')->label('Lavozim (UZ)')->required(),
                                    TinyEditor::make('content_uz')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tab::make('Ru')->schema([
                                    TextInput::make('name_ru')->label('Ism (RU)'),
                                    TextInput::make('position_ru')->label('Lavozim (RU)'),
                                    TinyEditor::make('content_ru')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tab::make('En')->schema([
                                    TextInput::make('name_en')->label('Ism (EN)'),
                                    TextInput::make('position_en')->label('Lavozim (EN)'),
                                    TinyEditor::make('content_en')->showMenuBar()->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Foydalanuvchi va sahifalar')
                    ->schema([
                        Select::make('user_id')
                            ->label('Foydalanuvchi')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->visible(fn(): bool => authUser()?->hasRole('super-admin')),

                        Select::make('page_id')
                            ->label('Sahifa')
                            ->options(function () {
                                return Page::whereIn('page_type', ['faculty', 'department', 'center', 'section'])
                                    ->pluck('title_uz', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Select::make('staff_category_id')
                            ->label('Xodim kategoriyasi')
                            ->options(function (callable $get) {
                                $pageId = $get('page_id');
                                if (!$pageId) return [];

                                return StaffCategory::where('page_id', $pageId)
                                    ->pluck('title_uz', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->reactive(),

                        Select::make('pages')
                            ->label('Tahrirlash mumkin bo‘lgan sahifalar')
                            ->relationship('pages', 'title_uz')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn(): bool => authUser()?->hasRole('super-admin')),
                    ])
                    ->columns(4),

                Section::make('Rasm')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Rasm')
                            ->collection('image'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name_uz')->label('Ism')->searchable(),
                TextColumn::make('position_uz')->label('Lavozim'),
                TextColumn::make('user.name')->label('Foydalanuvchi'),
                TextColumn::make('page.title_uz')->label('Tegishli sahifa'),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                SpatieMediaLibraryImageColumn::make('image')->collection('image')->conversion('thumb')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('page_id')
                    ->label('Sahifa')
                    ->options(function () {
                        return Page::whereIn('page_type', ['faculty', 'department', 'center', 'section'])
                            ->orderBy('title_uz')
                            ->pluck('title_uz', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->multiple()  // Bir nechta sahifani tanlash imkoniyati
                    ->placeholder('Sahifani tanlang'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffMembers::route('/'),
            'create' => CreateStaffMember::route('/create'),
            'edit' => EditStaffMember::route('/{record}/edit'),
        ];
    }
}
