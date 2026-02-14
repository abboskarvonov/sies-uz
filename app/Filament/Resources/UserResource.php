<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Foydalanuvchilar';

    protected static ?string $pluralModelLabel = 'Foydalanuvchilar';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return authUser()?->hasRole('super-admin');
    }

    public static function form(Form $form): Form
    {
        return $form
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
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ism'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('last_seen_at')
                    ->label('Oxirgi onlayn')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('last_seen_at')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '⚫ Offline';
                        }
                        return now()->diffInMinutes($state, true) < 2 ? '🟢 Online' : '⚫ Offline';
                    })
                    ->sortable(),

            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
