<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Models\StaffCategory;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'staffMembers';

    protected static ?string $title = 'Xodimlar';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->page_type, ['department', 'faculty', 'center', 'section']);
    }

    public function form(Schema $schema): Schema
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

                Section::make('Qo\'shimcha')
                    ->schema([
                        Select::make('user_id')
                            ->label('Foydalanuvchi')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->nullable(),

                        Select::make('staff_category_id')
                            ->label('Xodim kategoriyasi')
                            ->options(fn() => StaffCategory::where('page_id', $this->getOwnerRecord()->id)
                                ->pluck('title_uz', 'id'))
                            ->searchable()
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Rasm')
                            ->collection('image'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_uz')->label('Ism')->searchable(),
                TextColumn::make('position_uz')->label('Lavozim'),
                TextColumn::make('staffCategory.title_uz')->label('Kategoriya'),
                TextColumn::make('user.name')->label('Foydalanuvchi'),
                SpatieMediaLibraryImageColumn::make('image')->collection('image')->conversion('thumb')->label('Rasm'),
            ])
            ->filters([
                SelectFilter::make('staff_category_id')
                    ->label('Kategoriya')
                    ->options(fn() => StaffCategory::where('page_id', $this->getOwnerRecord()->id)
                        ->pluck('title_uz', 'id'))
                    ->searchable()
                    ->preload(),
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
