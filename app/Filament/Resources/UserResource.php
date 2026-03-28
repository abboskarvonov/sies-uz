<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Pages\Page;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components;
use App\Filament\Concerns\HasSpatiePermissions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    use HasSpatiePermissions;

    protected static string $permPrefix = 'User';

    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Foydalanuvchilar';

    protected static ?string $pluralModelLabel = 'Foydalanuvchilar';

    protected static ?int $navigationSort = 1;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shaxsiy ma\'lumotlar')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ism')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Parol')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'O\'zgartirmasangiz bo\'sh qoldiring' : null),

                        Toggle::make('email_verified')
                            ->label('Email tasdiqlangan')
                            ->dehydrated(false)
                            ->afterStateHydrated(fn ($component, $record) => $component->state($record?->email_verified_at !== null))
                            ->visible(fn (): bool => authUser()?->hasRole('super-admin')),
                    ])
                    ->columns(2),

                Section::make('Rollar va huquqlar')
                    ->schema([
                        Select::make('roles')
                            ->label('Rollar')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload(),

                        Select::make('permissions')
                            ->label('Huquqlar')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Biriktirilgan sahifalar')
                    ->description('Foydalanuvchi faqat shu sahifalar va ularga tegishli ma\'lumotlarni (fayllar, xodimlar, kategoriyalar, tarix) tahrirlashi mumkin')
                    ->schema([
                        Select::make('assignedPages')
                            ->label('Sahifalar')
                            ->relationship('assignedPages', 'title_uz')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),

                Section::make('HEMIS ulash')
                    ->description('Agar bu foydalanuvchi HEMIS dagi xodim bilan bir xil shaxs bo\'lsa, HEMIS Employee ID ni kiriting. Keyingi sync da avtomatik topiladi.')
                    ->schema([
                        TextInput::make('hemis_employee_id')
                            ->label('HEMIS Employee ID (employee-list ID)')
                            ->placeholder('Masalan: 417')
                            ->helperText('data/employee-list → id maydoni. Sync action "Faqat tekshirish" da ko\'rsatiladi.')
                            ->unique(ignoreRecord: true)
                            ->nullable(),

                        TextInput::make('hemis_id')
                            ->label('HEMIS OAuth ID')
                            ->placeholder('Masalan: 397')
                            ->helperText('OAuth orqali kirganda avtomatik to\'ldiriladi.')
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Lavozimlar')
                    ->description('Xodimning barcha lavozimlari (user_page_positions). Noto\'g\'ri lavozimni o\'chirib, xodim qayta login qilishi mumkin.')
                    ->schema([
                        Repeater::make('pagePositions')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Select::make('page_id')
                                    ->label('Bo\'lim / Kafedra / Fakultet')
                                    ->relationship('page', 'title_uz')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('position_uz')->label('Lavozim (UZ)'),
                                TextInput::make('position_ru')->label('Lavozim (RU)'),
                                TextInput::make('position_en')->label('Lavozim (EN)'),

                                Toggle::make('is_primary')
                                    ->label('Asosiy lavozim')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->addActionLabel('Lavozim qo\'shish')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['position_uz'] ?? null),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn (): bool => authUser()?->hasRole('super-admin')),

                Section::make('HEMIS xodim ma\'lumotlari')
                    ->description('HEMIS dan sync qilinadi. Super-admin tomonidan ham tahrirlash mumkin.')
                    ->schema([
                        TextInput::make('position_uz')->label('Lavozim (UZ)')->maxLength(255),
                        TextInput::make('position_ru')->label('Lavozim (RU)')->maxLength(255),
                        TextInput::make('position_en')->label('Lavozim (EN)')->maxLength(255),
                        TextInput::make('academic_degree')->label('Ilmiy daraja')->maxLength(255),
                        TextInput::make('academic_rank')->label('Ilmiy unvon')->maxLength(255),
                        TextInput::make('employment_form')->label('Bandlik shakli')->maxLength(255),

                        Select::make('department_page_id')
                            ->label('Bo\'lim / Kafedra')
                            ->relationship('departmentPage', 'title_uz')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('staff_category_id')
                            ->label('Xodim kategoriyasi')
                            ->relationship('staffCategory', 'title_uz')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Textarea::make('content_uz')->label('Bio (UZ)')->rows(4)->columnSpanFull(),
                        Textarea::make('content_ru')->label('Bio (RU)')->rows(4)->columnSpanFull(),
                        Textarea::make('content_en')->label('Bio (EN)')->rows(4)->columnSpanFull(),

                        FileUpload::make('profile_photo_path')
                            ->label('Profil rasmi')
                            ->image()
                            ->disk('public')
                            ->directory('profile-photos')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn (): bool => authUser()?->hasRole('super-admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0d9488&background=f0fdfa'),

                TextColumn::make('name')->label('Ism')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hemis_id')->label('HEMIS ID')->searchable()->toggleable(isToggledHiddenByDefault: true)->copyable()->placeholder('—'),

                TextColumn::make('hemis_type')
                    ->label('Turi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'employee' => 'info',
                        'student'  => 'warning',
                        'admin'    => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('position_uz')
                    ->label('Lavozim')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('departmentPage.title_uz')
                    ->label('Bo\'lim')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label('Rollar')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assigned_pages_count')
                    ->counts('assignedPages')
                    ->label('Sahifalar')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('email_verified_at')
                    ->label('Tasdiqlangan')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_seen_at')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Offline';
                        }
                        return now()->diffInMinutes($state, true) < 2 ? 'Online' : 'Offline';
                    })
                    ->badge()
                    ->color(fn ($state) => ! empty($state) && now()->diffInMinutes($state, true) < 2 ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('hemis_type')
                    ->label('Turi')
                    ->options([
                        'admin'    => 'Admin',
                        'employee' => 'Xodim',
                        'student'  => 'Talaba',
                    ]),
                SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name'),
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
                Section::make('Foydalanuvchi')
                    ->schema([
                        ImageEntry::make('profile_photo_path')
                            ->label('Rasm')
                            ->disk('public')
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0d9488&background=f0fdfa'),
                        TextEntry::make('name')->label('Ism'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('hemis_type')
                            ->label('Turi')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'employee' => 'info',
                                'student'  => 'warning',
                                'admin'    => 'danger',
                                default    => 'gray',
                            }),
                        IconEntry::make('email_verified_at')
                            ->label('Email tasdiqlangan')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Ro\'yxatdan o\'tgan')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('roles.name')
                            ->label('Rollar')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('permissions.name')
                            ->label('Maxsus huquqlar')
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('last_seen_at')
                            ->label('Oxirgi faollik')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(3),

                Section::make('HEMIS ma\'lumotlari')
                    ->schema([
                        TextEntry::make('hemis_id')->label('HEMIS ID'),
                        TextEntry::make('position_uz')->label('Lavozim'),
                        TextEntry::make('academic_degree')->label('Ilmiy daraja'),
                        TextEntry::make('academic_rank')->label('Ilmiy unvon'),
                        TextEntry::make('employment_form')->label('Bandlik shakli'),
                        TextEntry::make('position_order')->label('Tartib raqami'),
                        TextEntry::make('departmentPage.title_uz')->label('Bo\'lim / Kafedra'),
                        TextEntry::make('staffCategory.title_uz')->label('Xodim kategoriyasi'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record?->hemis_type === 'employee'),

                Section::make('Biriktirilgan sahifalar')
                    ->schema([
                        RepeatableEntry::make('assignedPages')
                            ->label('')
                            ->schema([
                                TextEntry::make('title_uz')
                                    ->label('Sahifa')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('page_type')
                                    ->label('Turi')
                                    ->badge()
                                    ->color('gray'),
                            ])
                            ->columns(2)
                            ->contained(false),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewUser::class,
            EditUser::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}
