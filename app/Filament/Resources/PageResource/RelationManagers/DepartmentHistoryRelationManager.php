<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'departmentHistory';

    protected static ?string $title = 'Kafedra tarixi';

    public function isReadOnly(): bool
    {
        return $this->getOwnerRecord()->page_type !== 'department';
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->page_type === 'department';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Uz')->schema([
                            TinyEditor::make('content_uz')->label('Kontent (UZ)')->showMenuBar()->required()->columnSpanFull(),
                        ]),
                        Tab::make('Ru')->schema([
                            TinyEditor::make('content_ru')->label('Kontent (RU)')->showMenuBar()->columnSpanFull(),
                        ]),
                        Tab::make('En')->schema([
                            TinyEditor::make('content_en')->label('Kontent (EN)')->showMenuBar()->columnSpanFull(),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content_uz')
                    ->label('Kontent (UZ)')
                    ->html()
                    ->limit(100),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                TextColumn::make('created_at')->label('Yaratilgan')->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
