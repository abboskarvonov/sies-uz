<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Rollar';

    protected static ?string $pluralModelLabel = 'Rollar';

    protected static ?string $modelLabel = 'Rol';

    protected static ?int $navigationSort = 2;

    // ─── Tab tuzilishi ────────────────────────────────────────────────
    // tab_label => [ resource_key => [ label, perms ] ]
    //
    // Har bir resource_key → form field nomi: perm_{resource_key}
    // Permissions qo'lda yoziladi — Shield bilan aralashmaydi.

    public const TABS = [
        'Sahifalar' => [
            'faculty' => [
                'label' => 'Fakultetlar',
                'perms' => [
                    'faculty.viewAny'   => "Ko'rish (ro'yxat)",
                    'faculty.create'    => 'Yaratish',
                    'faculty.update'    => 'Tahrirlash',
                    'faculty.delete'    => "O'chirish",
                    'faculty.deleteAny' => "Barchasini o'chirish",
                ],
            ],
            'department' => [
                'label' => 'Kafedralar',
                'perms' => [
                    'department.viewAny'   => "Ko'rish (ro'yxat)",
                    'department.create'    => 'Yaratish',
                    'department.update'    => 'Tahrirlash',
                    'department.delete'    => "O'chirish",
                    'department.deleteAny' => "Barchasini o'chirish",
                ],
            ],
            'section' => [
                'label' => "Bo'limlar",
                'perms' => [
                    'section.viewAny'   => "Ko'rish (ro'yxat)",
                    'section.create'    => 'Yaratish',
                    'section.update'    => 'Tahrirlash',
                    'section.delete'    => "O'chirish",
                    'section.deleteAny' => "Barchasini o'chirish",
                ],
            ],
            'center' => [
                'label' => 'Markazlar',
                'perms' => [
                    'center.viewAny'   => "Ko'rish (ro'yxat)",
                    'center.create'    => 'Yaratish',
                    'center.update'    => 'Tahrirlash',
                    'center.delete'    => "O'chirish",
                    'center.deleteAny' => "Barchasini o'chirish",
                ],
            ],
            'boshqarma' => [
                'label' => 'Boshqarmalar',
                'perms' => [
                    'boshqarma.viewAny'   => "Ko'rish (ro'yxat)",
                    'boshqarma.create'    => 'Yaratish',
                    'boshqarma.update'    => 'Tahrirlash',
                    'boshqarma.delete'    => "O'chirish",
                    'boshqarma.deleteAny' => "Barchasini o'chirish",
                ],
            ],
            'page' => [
                'label' => 'Sahifalar (Blog / Default)',
                'perms' => [
                    'page.viewAny'   => "Ko'rish (ro'yxat)",
                    'page.create'    => 'Yaratish',
                    'page.update'    => 'Tahrirlash',
                    'page.delete'    => "O'chirish",
                    'page.deleteAny' => "Barchasini o'chirish",
                ],
            ],
        ],

        'Menyular' => [
            'menu' => [
                'label' => 'Menyular',
                'perms' => [
                    'ViewAny:Menu'   => "Ko'rish (ro'yxat)",
                    'Create:Menu'    => 'Yaratish',
                    'Update:Menu'    => 'Tahrirlash',
                    'Delete:Menu'    => "O'chirish",
                    'DeleteAny:Menu' => "Barchasini o'chirish",
                ],
            ],
            'submenu' => [
                'label' => 'Submenyular',
                'perms' => [
                    'ViewAny:Submenu'   => "Ko'rish (ro'yxat)",
                    'Create:Submenu'    => 'Yaratish',
                    'Update:Submenu'    => 'Tahrirlash',
                    'Delete:Submenu'    => "O'chirish",
                    'DeleteAny:Submenu' => "Barchasini o'chirish",
                ],
            ],
            'multimenu' => [
                'label' => 'Multimenyular',
                'perms' => [
                    'ViewAny:Multimenu'   => "Ko'rish (ro'yxat)",
                    'Create:Multimenu'    => 'Yaratish',
                    'Update:Multimenu'    => 'Tahrirlash',
                    'Delete:Multimenu'    => "O'chirish",
                    'DeleteAny:Multimenu' => "Barchasini o'chirish",
                ],
            ],
        ],

        'Kontent' => [
            'tag' => [
                'label' => 'Teglar',
                'perms' => [
                    'ViewAny:Tag' => "Ko'rish (ro'yxat)",
                    'Create:Tag'  => 'Yaratish',
                    'Update:Tag'  => 'Tahrirlash',
                    'Delete:Tag'  => "O'chirish",
                ],
            ],
            'symbol' => [
                'label' => 'Ramzlar (Symbols)',
                'perms' => [
                    'ViewAny:Symbol' => "Ko'rish (ro'yxat)",
                    'Create:Symbol'  => 'Yaratish',
                    'Update:Symbol'  => 'Tahrirlash',
                    'Delete:Symbol'  => "O'chirish",
                ],
            ],
        ],

        'Tizim' => [
            'stat' => [
                'label' => 'Statistika',
                'perms' => [
                    'ViewAny:SiteStat' => "Ko'rish (ro'yxat)",
                    'Create:SiteStat'  => 'Yaratish',
                    'Update:SiteStat'  => 'Tahrirlash',
                    'Delete:SiteStat'  => "O'chirish",
                ],
            ],
            'settings' => [
                'label' => 'Sayt sozlamalari',
                'perms' => [
                    'View:SiteSettings'   => "Ko'rish",
                    'Update:SiteSettings' => 'Tahrirlash',
                ],
            ],
            'user' => [
                'label' => 'Foydalanuvchilar',
                'perms' => [
                    'ViewAny:User' => "Ko'rish (ro'yxat)",
                    'Create:User'  => 'Yaratish',
                    'Update:User'  => 'Tahrirlash',
                    'Delete:User'  => "O'chirish",
                ],
            ],
            'special' => [
                'label' => 'Maxsus',
                'perms' => [
                    'access_filament_panel' => 'Admin panelga kirish',
                    'view_all_pages'        => "Barcha sahifalarni ko'rish",
                    'view_blog_pages'       => 'Faqat blog sahifalarni ko\'rish',
                ],
            ],
        ],
    ];

    // ─── Barcha resource keylarni qaytaradi (fill/save uchun) ─────────

    public static function allResourceKeys(): array
    {
        $keys = [];
        foreach (self::TABS as $resources) {
            $keys = array_merge($keys, array_keys($resources));
        }
        return $keys;
    }

    // ─── Tab building ─────────────────────────────────────────────────

    protected static function buildPermissionTabs(): array
    {
        $tabs = [];

        foreach (self::TABS as $tabLabel => $resources) {
            $lists = [];

            foreach ($resources as $key => $config) {
                $lists[] = CheckboxList::make("perm_{$key}")
                    ->label($config['label'])
                    ->options($config['perms'])
                    ->columns(3)
                    ->bulkToggleable()
                    ->gridDirection('row');
            }

            $tabs[] = Tab::make($tabLabel)->schema($lists);
        }

        return $tabs;
    }

    // ─── Form ─────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rol nomi')->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Placeholder::make('stats')
                    ->label('Statistika')
                    ->content(fn (?Role $record): string => $record
                        ? "Foydalanuvchilar: {$record->users()->count()} | Permissionlar: {$record->permissions()->count()}"
                        : '')
                    ->hidden(fn (?Role $record) => $record === null),
            ])->columns(2),

            Section::make('Huquqlar')->schema([
                Tabs::make('permission_tabs')
                    ->tabs(static::buildPermissionTabs())
                    ->columnSpanFull(),
            ]),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin' => 'danger',
                        'admin'       => 'warning',
                        default       => 'primary',
                    }),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissionlar')
                    ->badge()
                    ->color('info'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Foydalanuvchilar')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Role $record) {
                        if ($record->name === 'super-admin') {
                            throw new \Exception("super-admin rolini o'chirish mumkin emas.");
                        }
                    }),
            ]);
    }

    // ─── Infolist ─────────────────────────────────────────────────────

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rol')->schema([
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('permissions.name')
                    ->label('Permissionlar')
                    ->badge()
                    ->color('info')
                    ->separator(','),
            ]),
        ]);
    }

    // ─── Authorization ────────────────────────────────────────────────

    public static function canViewAny(): bool
    {
        return authUser()?->hasRole('super-admin') ?? false;
    }

    // ─── Pages ────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view'   => Pages\ViewRole::route('/{record}'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
