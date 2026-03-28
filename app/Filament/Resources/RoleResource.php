<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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

    // ─── Permissions shown in the form ────────────────────────────────
    // Faqat shu permissionlar RoleResource formida ko'rsatiladi.
    // Bu yerga qo'shilmagan eski Shield permissionlari formda chiqmaydi.

    private const PERMISSION_GROUPS = [
        'Panel kirish' => [
            'access_filament_panel' => 'Admin panelga kirish',
        ],

        'Fakultetlar' => [
            'faculty.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'faculty.create'    => 'Yaratish',
            'faculty.update'    => 'Tahrirlash',
            'faculty.delete'    => 'O\'chirish',
            'faculty.deleteAny' => 'Barchasini o\'chirish',
        ],

        'Kafedralar' => [
            'department.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'department.create'    => 'Yaratish',
            'department.update'    => 'Tahrirlash',
            'department.delete'    => 'O\'chirish',
            'department.deleteAny' => 'Barchasini o\'chirish',
        ],

        "Bo'limlar" => [
            'section.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'section.create'    => 'Yaratish',
            'section.update'    => 'Tahrirlash',
            'section.delete'    => 'O\'chirish',
            'section.deleteAny' => 'Barchasini o\'chirish',
        ],

        'Markazlar' => [
            'center.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'center.create'    => 'Yaratish',
            'center.update'    => 'Tahrirlash',
            'center.delete'    => 'O\'chirish',
            'center.deleteAny' => 'Barchasini o\'chirish',
        ],

        'Boshqarmalar' => [
            'boshqarma.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'boshqarma.create'    => 'Yaratish',
            'boshqarma.update'    => 'Tahrirlash',
            'boshqarma.delete'    => 'O\'chirish',
            'boshqarma.deleteAny' => 'Barchasini o\'chirish',
        ],

        'Sahifalar (Blog / Default)' => [
            'page.viewAny'   => 'Ko\'rish (ro\'yxat)',
            'page.create'    => 'Yaratish',
            'page.update'    => 'Tahrirlash',
            'page.delete'    => 'O\'chirish',
            'page.deleteAny' => 'Barchasini o\'chirish',
        ],

        'Sahifalarga umumiy kirish' => [
            'view_all_pages'  => 'Barcha sahifalarni ko\'rish (query filter)',
            'view_blog_pages' => 'Faqat blog sahifalarni ko\'rish',
        ],

        'Menyular' => [
            'ViewAny:Menu'    => 'Ko\'rish (ro\'yxat)',
            'Create:Menu'     => 'Yaratish',
            'Update:Menu'     => 'Tahrirlash',
            'Delete:Menu'     => 'O\'chirish',
            'DeleteAny:Menu'  => 'Barchasini o\'chirish',
        ],

        'Submenyular' => [
            'ViewAny:Submenu'   => 'Ko\'rish (ro\'yxat)',
            'Create:Submenu'    => 'Yaratish',
            'Update:Submenu'    => 'Tahrirlash',
            'Delete:Submenu'    => 'O\'chirish',
            'DeleteAny:Submenu' => 'Barchasini o\'chirish',
        ],

        'Multimenyular' => [
            'ViewAny:Multimenu'   => 'Ko\'rish (ro\'yxat)',
            'Create:Multimenu'    => 'Yaratish',
            'Update:Multimenu'    => 'Tahrirlash',
            'Delete:Multimenu'    => 'O\'chirish',
            'DeleteAny:Multimenu' => 'Barchasini o\'chirish',
        ],

        'Teglar' => [
            'ViewAny:Tag'   => 'Ko\'rish (ro\'yxat)',
            'Create:Tag'    => 'Yaratish',
            'Update:Tag'    => 'Tahrirlash',
            'Delete:Tag'    => 'O\'chirish',
        ],

        'Ramzlar (Symbols)' => [
            'ViewAny:Symbol'   => 'Ko\'rish (ro\'yxat)',
            'Create:Symbol'    => 'Yaratish',
            'Update:Symbol'    => 'Tahrirlash',
            'Delete:Symbol'    => 'O\'chirish',
        ],

        'Statistika' => [
            'ViewAny:SiteStat' => 'Ko\'rish (ro\'yxat)',
            'Create:SiteStat'  => 'Yaratish',
            'Update:SiteStat'  => 'Tahrirlash',
            'Delete:SiteStat'  => 'O\'chirish',
        ],

        'Sayt sozlamalari' => [
            'View:SiteSettings'   => 'Ko\'rish',
            'Update:SiteSettings' => 'Tahrirlash',
        ],

        'Foydalanuvchilar' => [
            'ViewAny:User'   => 'Ko\'rish (ro\'yxat)',
            'Create:User'    => 'Yaratish',
            'Update:User'    => 'Tahrirlash',
            'Delete:User'    => 'O\'chirish',
        ],
    ];

    // ─── Form ─────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        $sections = [
            Section::make('Rol nomi')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                ])
                ->columns(1),
        ];

        foreach (self::PERMISSION_GROUPS as $groupLabel => $permissions) {
            $sections[] = Section::make($groupLabel)
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->relationship('permissions', 'name')
                        ->options($permissions)
                        ->columns(2)
                        ->gridDirection('row'),
                ])
                ->collapsible()
                ->collapsed(false);
        }

        return $schema->components($sections);
    }

    // ─── Table ────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

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
                        // super-admin rolini o'chirish mumkin emas
                        if ($record->name === 'super-admin') {
                            throw new \Exception('super-admin rolini o\'chirish mumkin emas.');
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
