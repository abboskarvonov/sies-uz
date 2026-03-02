<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingsResource\Pages;
use App\Models\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsResource extends Resource
{
    protected static ?string $model = SiteSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Sahifa va boshqa menyular';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationLabel = 'Sayt sozlamalari';
    protected static ?string $pluralModelLabel = 'Sayt sozlamalari';
    protected static ?string $modelLabel = 'Sayt sozlamasi';

    public static function canCreate(): bool
    {
        return SiteSettings::count() === 0;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Sayt nomi (3 tilda)')
                ->schema([
                    TextInput::make('site_name_uz')->label("O'zbekcha nom")->maxLength(255),
                    TextInput::make('site_name_ru')->label('Ruscha nom')->maxLength(255),
                    TextInput::make('site_name_en')->label('Inglizcha nom')->maxLength(255),
                ])->columns(3),

            Section::make('Manzil (3 tilda)')
                ->schema([
                    TextInput::make('address_uz')->label("O'zbekcha manzil")->maxLength(500),
                    TextInput::make('address_ru')->label('Ruscha manzil')->maxLength(500),
                    TextInput::make('address_en')->label('Inglizcha manzil')->maxLength(500),
                ])->columns(3),

            Section::make('Telefon va Email')
                ->schema([
                    TextInput::make('phone_primary')
                        ->label('Asosiy telefon')
                        ->tel()
                        ->placeholder('+998 66 231 12 53')
                        ->maxLength(30),
                    TextInput::make('phone_secondary')
                        ->label('Qo\'shimcha telefon')
                        ->tel()
                        ->maxLength(30),
                    TextInput::make('email_primary')
                        ->label('Asosiy email')
                        ->email()
                        ->placeholder('info@sies.uz')
                        ->maxLength(255),
                    TextInput::make('email_secondary')
                        ->label('Qo\'shimcha email')
                        ->email()
                        ->maxLength(255),
                ])->columns(2),

            Section::make('Ijtimoiy tarmoqlar')
                ->schema([
                    TextInput::make('telegram_url')
                        ->label('Telegram')
                        ->url()
                        ->placeholder('https://t.me/...')
                        ->maxLength(500),
                    TextInput::make('facebook_url')
                        ->label('Facebook')
                        ->url()
                        ->placeholder('https://facebook.com/...')
                        ->maxLength(500),
                    TextInput::make('instagram_url')
                        ->label('Instagram')
                        ->url()
                        ->placeholder('https://instagram.com/...')
                        ->maxLength(500),
                    TextInput::make('youtube_url')
                        ->label('YouTube')
                        ->url()
                        ->placeholder('https://youtube.com/...')
                        ->maxLength(500),
                ])->columns(2),

            Section::make('Tashqi tizimlar')
                ->schema([
                    TextInput::make('hemis_url')
                        ->label('HEMIS URL')
                        ->url()
                        ->placeholder('https://student.sies.uz/...')
                        ->maxLength(500),
                    TextInput::make('arm_url')
                        ->label('ARM URL')
                        ->url()
                        ->placeholder('https://arm.sies.uz/')
                        ->maxLength(500),
                    TextInput::make('sdg_url')
                        ->label('SDG URL')
                        ->url()
                        ->placeholder('https://sdg.sies.uz/')
                        ->maxLength(500),
                ])->columns(3),

            Section::make('Logo')
                ->schema([
                    FileUpload::make('logo')
                        ->label('Sayt logosi')
                        ->disk('public')
                        ->directory('site')
                        ->imagePreviewHeight('80')
                        ->maxSize(2048),
                ]),

            Section::make('Google Maps')
                ->schema([
                    Textarea::make('map_embed_url')
                        ->label('Google Maps embed URL (src qiymati)')
                        ->placeholder('https://www.google.com/maps/embed?pb=...')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->defaultImageUrl(asset('img/logo.webp')),
                TextColumn::make('site_name_uz')->label('Sayt nomi (UZ)')->limit(40),
                TextColumn::make('phone_primary')->label('Telefon'),
                TextColumn::make('email_primary')->label('Email'),
                TextColumn::make('updated_at')->dateTime()->label('Yangilandi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSettings::route('/create'),
            'edit'   => Pages\EditSiteSettings::route('/{record}/edit'),
        ];
    }
}
