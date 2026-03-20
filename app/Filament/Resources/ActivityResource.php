<?php

namespace App\Filament\Resources;

use Illuminate\Support\Collection;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\ActivityResource\Pages\ListActivities;
use App\Filament\Resources\ActivityResource\Pages\ViewActivity;
use Illuminate\Support\Str;
use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Infolists\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Loglar';

    protected static ?string $pluralModelLabel = 'Loglar';

    protected static ?int $navigationSort = 10;

    private static array $modelLabels = [
        'App\Models\Page' => 'Sahifa',
        'App\Models\Menu' => 'Menyu',
        'App\Models\Submenu' => 'Ichki menyu',
        'App\Models\Multimenu' => 'Multi menyu',
        'App\Models\Tag' => 'Teg',
        'App\Models\User' => 'Foydalanuvchi',
        'App\Models\Role' => 'Rol',
        'App\Models\Permission' => 'Huquq',
        'App\Models\StaffMember' => 'Xodim',
        'App\Models\StaffCategory' => 'Xodim kategoriyasi',
        'App\Models\SiteStat' => 'Ko\'rsatkich',
        'App\Models\Symbol' => 'Ramz',
        'App\Models\PageFile' => 'Sahifa fayl',
        'App\Models\DepartmentHistory' => 'Kafedra tarixi',
    ];

    private static array $eventLabels = [
        'created' => 'Yaratildi',
        'updated' => 'Tahrirlandi',
        'deleted' => 'O\'chirildi',
    ];

    private static array $eventColors = [
        'created' => 'success',
        'updated' => 'warning',
        'deleted' => 'danger',
    ];

    private static array $eventIcons = [
        'created' => 'heroicon-o-plus-circle',
        'updated' => 'heroicon-o-pencil-square',
        'deleted' => 'heroicon-o-trash',
    ];

    public static function getSubjectLabel(string $subjectType): string
    {
        return self::$modelLabels[$subjectType] ?? class_basename($subjectType);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Vaqt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('Foydalanuvchi')
                    ->default('Tizim')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('event')
                    ->label('Harakat')
                    ->badge()
                    ->color(fn (string $state) => self::$eventColors[$state] ?? 'gray')
                    ->icon(fn (string $state) => self::$eventIcons[$state] ?? 'heroicon-o-information-circle')
                    ->formatStateUsing(fn (string $state) => self::$eventLabels[$state] ?? ucfirst($state)),

                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state) => self::getSubjectLabel($state))
                    ->badge()
                    ->color('info'),

                TextColumn::make('description')
                    ->label('Tavsif')
                    ->formatStateUsing(function (Activity $record) {
                        $model = self::getSubjectLabel($record->subject_type);
                        $event = self::$eventLabels[$record->event] ?? $record->event;
                        $name = self::getRecordName($record);

                        return "$model #$record->subject_id" . ($name ? " ($name)" : '') . " — $event";
                    })
                    ->wrap()
                    ->searchable(),

                TextColumn::make('changes')
                    ->label('O\'zgarishlar')
                    ->getStateUsing(function (Activity $record) {
                        if ($record->event === 'created') {
                            return 'Yangi yozuv yaratildi';
                        }

                        if ($record->event === 'deleted') {
                            return 'Yozuv o\'chirildi';
                        }

                        $properties = is_string($record->properties)
                            ? json_decode($record->properties, true)
                            : ($record->properties instanceof Collection ? $record->properties->toArray() : $record->properties);

                        if (! $properties || ! isset($properties['old'], $properties['attributes'])) {
                            return '-';
                        }

                        $changes = self::getChangedFields($properties['old'], $properties['attributes']);

                        if (empty($changes)) {
                            return 'O\'zgarish yo\'q';
                        }

                        return count($changes) . ' ta maydon o\'zgardi: ' . implode(', ', array_keys($changes));
                    })
                    ->wrap()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Harakat')
                    ->options(self::$eventLabels),

                SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options(
                        collect(self::$modelLabels)
                            ->mapWithKeys(fn ($label, $class) => [$class => $label])
                            ->toArray()
                    ),

                SelectFilter::make('causer_id')
                    ->label('Foydalanuvchi')
                    ->relationship('causer', 'name'),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Boshlanish sanasi'),
                        DatePicker::make('until')
                            ->label('Tugash sanasi'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
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
                Section::make('Log ma\'lumotlari')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Vaqt')
                            ->dateTime('d.m.Y H:i:s'),
                        TextEntry::make('causer.name')
                            ->label('Foydalanuvchi')
                            ->default('Tizim'),
                        TextEntry::make('event')
                            ->label('Harakat')
                            ->badge()
                            ->color(fn (string $state) => self::$eventColors[$state] ?? 'gray')
                            ->formatStateUsing(fn (string $state) => self::$eventLabels[$state] ?? ucfirst($state)),
                        TextEntry::make('subject_type')
                            ->label('Model')
                            ->formatStateUsing(fn (string $state) => self::getSubjectLabel($state)),
                        TextEntry::make('subject_id')
                            ->label('Yozuv ID'),
                    ])
                    ->columns(3),

                Section::make('O\'zgarishlar')
                    ->schema([
                        TextEntry::make('properties')
                            ->label('')
                            ->columnSpanFull()
                            ->html()
                            ->getStateUsing(function (Activity $record) {
                                $properties = is_string($record->properties)
                                    ? json_decode($record->properties, true)
                                    : ($record->properties instanceof Collection ? $record->properties->toArray() : $record->properties);

                                if (! $properties) {
                                    return '<p class="text-gray-500">Ma\'lumot yo\'q</p>';
                                }

                                if ($record->event === 'created') {
                                    return self::renderCreatedTable($properties['attributes'] ?? []);
                                }

                                if ($record->event === 'deleted') {
                                    return self::renderDeletedTable($properties['old'] ?? $properties['attributes'] ?? []);
                                }

                                if (isset($properties['old'], $properties['attributes'])) {
                                    return self::renderChangesTable($properties['old'], $properties['attributes']);
                                }

                                return '<p class="text-gray-500">Ma\'lumot yo\'q</p>';
                            }),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'view' => ViewActivity::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    private static function getRecordName(Activity $record): ?string
    {
        $properties = is_string($record->properties)
            ? json_decode($record->properties, true)
            : ($record->properties instanceof Collection ? $record->properties->toArray() : $record->properties);

        $attrs = $properties['attributes'] ?? $properties['old'] ?? [];

        foreach (['title_uz', 'name', 'title', 'label'] as $field) {
            if (! empty($attrs[$field])) {
                return Str::limit($attrs[$field], 40);
            }
        }

        return null;
    }

    private static function getChangedFields(array $old, array $new): array
    {
        $skip = ['updated_at', 'created_at', 'remember_token'];
        $changes = [];

        foreach ($new as $key => $value) {
            if (in_array($key, $skip)) {
                continue;
            }
            if (! array_key_exists($key, $old) || $old[$key] !== $value) {
                $changes[$key] = [
                    'old' => $old[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    private static function renderCreatedTable(array $attributes): string
    {
        $skip = ['id', 'created_at', 'updated_at', 'remember_token', 'password'];
        $html = '<table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left p-2">Maydon</th><th class="text-left p-2">Qiymat</th></tr></thead><tbody>';

        foreach ($attributes as $key => $value) {
            if (in_array($key, $skip) || is_null($value) || $value === '' || $value === '[]') {
                continue;
            }
            $displayValue = self::formatValue($value);
            $html .= "<tr class=\"border-b\"><td class=\"p-2 font-medium\">{$key}</td><td class=\"p-2 text-green-600\">{$displayValue}</td></tr>";
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private static function renderDeletedTable(array $attributes): string
    {
        $skip = ['id', 'created_at', 'updated_at', 'remember_token', 'password'];
        $html = '<table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left p-2">Maydon</th><th class="text-left p-2">Qiymat</th></tr></thead><tbody>';

        foreach ($attributes as $key => $value) {
            if (in_array($key, $skip) || is_null($value) || $value === '' || $value === '[]') {
                continue;
            }
            $displayValue = self::formatValue($value);
            $html .= "<tr class=\"border-b\"><td class=\"p-2 font-medium\">{$key}</td><td class=\"p-2 text-red-600 line-through\">{$displayValue}</td></tr>";
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private static function renderChangesTable(array $old, array $new): string
    {
        $changes = self::getChangedFields($old, $new);

        if (empty($changes)) {
            return '<p class="text-gray-500">Hech qanday o\'zgarish topilmadi</p>';
        }

        $html = '<table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left p-2">Maydon</th><th class="text-left p-2">Oldingi qiymat</th><th class="text-left p-2">Yangi qiymat</th></tr></thead><tbody>';

        foreach ($changes as $key => $change) {
            $oldVal = self::formatValue($change['old']);
            $newVal = self::formatValue($change['new']);
            $html .= "<tr class=\"border-b\"><td class=\"p-2 font-medium\">{$key}</td><td class=\"p-2 text-red-600\">{$oldVal}</td><td class=\"p-2 text-green-600\">{$newVal}</td></tr>";
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private static function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return '<span class="text-gray-400 italic">bo\'sh</span>';
        }

        if (is_bool($value)) {
            return $value ? 'Ha' : 'Yo\'q';
        }

        if (is_array($value)) {
            return e(json_encode($value, JSON_UNESCAPED_UNICODE));
        }

        $str = (string) $value;

        if (strlen($str) > 200) {
            $str = Str::limit(strip_tags($str), 200);
        }

        return e($str);
    }
}
