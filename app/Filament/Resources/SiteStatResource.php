<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteStatResource\Pages;
use App\Filament\Resources\SiteStatResource\RelationManagers;
use App\Models\SiteStat;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteStatResource extends Resource
{
    protected static ?string $model = SiteStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Ko‘rsatkichlar';
    protected static ?string $pluralModelLabel = 'Ko‘rsatkichlar';

    public static function canCreate(): bool
    {
        return SiteStat::count() === 0;
    }

    public static function form(Form $form): Form
    {
        $num = fn(string $label) => TextInput::make($label)->numeric()->minValue(0)->default(0);

        return $form
            ->schema([
                Section::make('Maydonlar')
                    ->schema([
                        $num('campus_area')->label('Maydon (m²)'),
                        $num('green_area')->label('Yashil hudud (m²)'),
                    ])->columns(2),

                Section::make('Tashkiliy tuzilma')
                    ->schema([
                        $num('faculties')->label('Fakultetlar'),
                        $num('departments')->label('Kafedralar'),
                        $num('centers')->label('Markaz va bo‘limlar'),
                    ])->columns(3),

                Section::make('Xodimlar')
                    ->schema([
                        $num('employees')->label('Xodimlar (jami)'),
                        $num('leadership')->label('Rahbariyat'),
                        $num('scientific')->label('Ilmiy xodimlar'),
                        $num('technical')->label('Texnik xodimlar'),
                    ])->columns(4),

                Section::make('Talabalar')
                    ->schema([
                        $num('students')->label('Talabalar (jami)'),
                        $num('male_students')->label('O‘g‘il bolalar'),
                        $num('female_students')->label('Qizlar'),
                    ])->columns(3),

                Section::make('O‘qituvchilar')
                    ->schema([
                        $num('teachers')->label('O‘qituvchilar (jami)'),
                        $num('dsi')->label('DSc'),
                        $num('phd_teachers')->label('PhD'),
                        $num('professors')->label('Dotsent/Prof.'),
                    ])->columns(4),

                Section::make('Nashrlar')
                    ->schema([
                        $num('books')->label('Nashrlar (jami)'),
                        $num('textbooks')->label('Darsliklar'),
                        $num('study')->label('O‘quv qo‘llanmalar'),
                        $num('methodological')->label('Uslubiy qo‘llanmalar'),
                        $num('monograph')->label('Monografiyalar'),
                    ])->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campus_area')->label('Maydon'),
                TextColumn::make('green_area')->label('Yashil hudud'),
                TextColumn::make('faculties')->label('Fakultetlar'),
                TextColumn::make('departments')->label('Kafedralar'),
                TextColumn::make('centers')->label('Markaz va bo‘limlar'),
                TextColumn::make('students')->label('Talabalar'),
                TextColumn::make('teachers')->label('O‘qituvchilar'),
                TextColumn::make('books')->label('Nashrlar'),
                TextColumn::make('updated_at')->dateTime()->label('Yangilandi'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSiteStats::route('/'),
            'create' => Pages\CreateSiteStat::route('/create'),
            'edit' => Pages\EditSiteStat::route('/{record}/edit'),
        ];
    }
}