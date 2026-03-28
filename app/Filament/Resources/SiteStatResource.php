<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SiteStatResource\Pages\ListSiteStats;
use App\Filament\Resources\SiteStatResource\Pages\CreateSiteStat;
use App\Filament\Resources\SiteStatResource\Pages\EditSiteStat;
use App\Filament\Resources\SiteStatResource\Pages;
use App\Models\SiteStat;
use Filament\Forms\Components\TextInput;
use App\Filament\Concerns\HasSpatiePermissions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteStatResource extends Resource
{
    use HasSpatiePermissions;

    protected static string $permPrefix = 'SiteStat';

    protected static ?string $model = SiteStat::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string | \UnitEnum | null $navigationGroup = 'Sahifa va boshqa menyular';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Ko‘rsatkichlar';
    protected static ?string $pluralModelLabel = 'Ko‘rsatkichlar';

    public static function canCreate(): bool
    {
        return SiteStat::count() === 0;
    }

    public static function form(Schema $schema): Schema
    {
        $num = fn(string $label) => TextInput::make($label)->numeric()->minValue(0)->default(0);

        return $schema
            ->components([
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListSiteStats::route('/'),
            'create' => CreateSiteStat::route('/create'),
            'edit' => EditSiteStat::route('/{record}/edit'),
        ];
    }
}
