<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use App\Models\StaffCategory;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $title = 'Xodimlar (HEMIS)';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->page_type, ['department', 'faculty', 'center', 'section']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('position_order')
            ->columns([
                ImageColumn::make('profile_photo_path')
                    ->label('Rasm')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0d9488&background=f0fdfa'),

                TextColumn::make('name')
                    ->label('Ism')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position_uz')
                    ->label('Lavozim')
                    ->searchable(),

                TextColumn::make('staffCategory.title_uz')
                    ->label('Kategoriya')
                    ->badge()
                    ->color('info'),

                TextColumn::make('academic_degree')
                    ->label('Ilmiy daraja')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('academic_rank')
                    ->label('Ilmiy unvon')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('position_order')
                    ->label('Tartib')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('staff_category_id')
                    ->label('Kategoriya')
                    ->options(fn () => StaffCategory::where('page_id', $this->getOwnerRecord()->id)
                        ->orderBy('order')
                        ->pluck('title_uz', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.users.view', $record)),
                Action::make('edit_employee')
                    ->label('Tahrirlash')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record))
                    ->visible(fn () => authUser()?->can('manage_own_page_staff')
                        || authUser()?->can('Update:User')
                        || authUser()?->hasRole('super-admin')),
            ])
            ->toolbarActions([]);
    }
}
