<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Models\StaffCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sarlovha va kontentlar')
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Uz')->schema([
                                    TextInput::make('name_uz')->label('Ism (UZ)')->required(),
                                    TextInput::make('position_uz')->label('Lavozim (UZ)')->required(),
                                    TinyEditor::make('content_uz')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tabs\Tab::make('Ru')->schema([
                                    TextInput::make('name_ru')->label('Ism (RU)'),
                                    TextInput::make('position_ru')->label('Lavozim (RU)'),
                                    TinyEditor::make('content_ru')->showMenuBar()->columnSpanFull(),
                                ]),
                                Tabs\Tab::make('En')->schema([
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

                        FileUpload::make('image')
                            ->label('Rasm')
                            ->disk('public')
                            ->directory('staff_members'),
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
                ImageColumn::make('image')->label('Rasm')->disk('public'),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
