<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Foydalanuvchilar';

    protected static ?string $pluralModelLabel = 'Foydalanuvchilar';

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ism')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rollar')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('assigned_pages_count')
                    ->counts('assignedPages')
                    ->label('Sahifalar')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('email_verified_at')
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
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name'),
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
                Components\Section::make('Foydalanuvchi')
                    ->schema([
                        Components\TextEntry::make('name')->label('Ism'),
                        Components\TextEntry::make('email')->label('Email'),
                        Components\IconEntry::make('email_verified_at')
                            ->label('Email tasdiqlangan')
                            ->boolean(),
                        Components\TextEntry::make('created_at')
                            ->label('Ro\'yxatdan o\'tgan')
                            ->dateTime('d.m.Y H:i'),
                        Components\TextEntry::make('roles.name')
                            ->label('Rollar')
                            ->badge()
                            ->color('primary'),
                        Components\TextEntry::make('permissions.name')
                            ->label('Maxsus huquqlar')
                            ->badge()
                            ->color('warning'),
                        Components\TextEntry::make('last_seen_at')
                            ->label('Oxirgi faollik')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),

                Components\Section::make('Biriktirilgan sahifalar')
                    ->schema([
                        Components\RepeatableEntry::make('assignedPages')
                            ->label('')
                            ->schema([
                                Components\TextEntry::make('title_uz')
                                    ->label('Sahifa')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('page_type')
                                    ->label('Turi')
                                    ->badge()
                                    ->color('gray'),
                            ])
                            ->columns(2)
                            ->contained(false),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(\Filament\Pages\Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
