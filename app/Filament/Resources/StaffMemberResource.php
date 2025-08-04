<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffMemberResource\Pages;
use App\Filament\Resources\StaffMemberResource\RelationManagers;
use App\Models\StaffMember;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffMemberResource extends Resource
{
    protected static ?string $model = StaffMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';

    protected static ?string $navigationLabel = 'Xodimlar';

    protected static ?string $pluralModelLabel = 'Xodimlar';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
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
                                    RichEditor::make('content_uz'),
                                ]),
                                Tabs\Tab::make('Ru')->schema([
                                    TextInput::make('name_ru')->label('Ism (RU)'),
                                    TextInput::make('position_ru')->label('Lavozim (RU)'),
                                    RichEditor::make('content_ru'),
                                ]),
                                Tabs\Tab::make('En')->schema([
                                    TextInput::make('name_en')->label('Ism (EN)'),
                                    TextInput::make('position_en')->label('Lavozim (EN)'),
                                    RichEditor::make('content_en'),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Foydalanuvchi va sahifalar')
                    ->schema([
                        Select::make('user_id')
                            ->label('Foydalanuvchi')
                            ->relationship('user', 'name'),

                        Select::make('page_id')
                            ->label('Sahifa')
                            ->relationship('page', 'title_uz')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('pages')
                            ->label('Tahrirlash mumkin bo‘lgan sahifalar')
                            ->relationship('pages', 'title_uz')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(3),

                Section::make('Rasm')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Rasm')
                            ->disk('public')
                            ->directory('staff_members')
                            ->preserveFilenames(),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name_uz')->label('Ism')->searchable(),
                TextColumn::make('position_uz')->label('Lavozim'),
                TextColumn::make('user.name')->label('Foydalanuvchi'),
                TextColumn::make('page.title_uz')->label('Tegishli sahifa'),
                TextColumn::make('pages')
                    ->label('Tahrirlash mumkin bo‘lgan sahifalar')
                    ->getStateUsing(
                        fn($record) =>
                        $record->pages
                            ->pluck('title_' . app()->getLocale())
                            ->implode(', ')
                    )
                    ->wrap(),
                TextColumn::make('createdBy.name')->label('Yaratuvchi'),
                TextColumn::make('updatedBy.name')->label('O\'zgartiruvchi'),
                ImageColumn::make('image')->label('Rasm'),
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
            'index' => Pages\ListStaffMembers::route('/'),
            'create' => Pages\CreateStaffMember::route('/create'),
            'edit' => Pages\EditStaffMember::route('/{record}/edit'),
        ];
    }
}
