<?php

namespace App\Filament\Actions;

use Filament\Schemas\Components\Utilities\Get;
use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class AssignMenuToFacultiesAction extends Action
{
    protected string $targetPageType = 'faculty';
    protected string $targetLabel    = 'fakultet';

    public static function getDefaultName(): ?string
    {
        return 'assignMenuToPages';
    }

    /** Qaysi page_type ga qo'llanishini belgilash */
    public function forPageType(string $pageType, string $label): static
    {
        $this->targetPageType = $pageType;
        $this->targetLabel    = $label;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Menyu biriktirish')
            ->icon('heroicon-o-link')
            ->color('gray')
            ->modalHeading(fn() => "Barcha {$this->targetLabel}larga menyu biriktirish")
            ->modalDescription(fn() => $this->targetPageType === 'section'
                ? "Tanlangan menyu barcha {$this->targetLabel} sahifalariga biriktiriladi. Ota sahifasi (markaz) mavjud bo'lganlar o'tkazib yuboriladi."
                : "Tanlangan menyu, submenyu va multimenu barcha {$this->targetLabel} sahifalariga biriktiriladi."
            )
            ->modalSubmitActionLabel('Biriktirish')
            ->schema([
                Select::make('menu_id')
                    ->label('Asosiy menyu')
                    ->options(Menu::orderBy('order')->pluck('title_uz', 'id'))
                    ->required()
                    ->live(),

                Select::make('submenu_id')
                    ->label('Submenyu')
                    ->options(fn(Get $get) => $get('menu_id')
                        ? Submenu::where('menu_id', $get('menu_id'))->orderBy('order')->pluck('title_uz', 'id')
                        : []
                    )
                    ->required()
                    ->live(),

                Select::make('multimenu_id')
                    ->label('Multimenu')
                    ->options(fn(Get $get) => $get('submenu_id')
                        ? Multimenu::where('submenu_id', $get('submenu_id'))->orderBy('order')->pluck('title_uz', 'id')
                        : []
                    )
                    ->required(),
            ])
            ->action(function (array $data): void {
                $query = Page::where('page_type', $this->targetPageType);

                // Bo'limlarda ota sahifasi mavjud bo'lganlarni o'tkazib yuborish
                // (ular markazga tegishli, alohida birikmaydi)
                if ($this->targetPageType === 'section') {
                    $query->whereNull('parent_page_id');
                }

                $count = $query->update([
                    'menu_id'      => $data['menu_id'],
                    'submenu_id'   => $data['submenu_id'],
                    'multimenu_id' => $data['multimenu_id'],
                ]);

                Notification::make()
                    ->title('Menyu biriktirildi')
                    ->body("{$count} ta {$this->targetLabel} sahifasiga menyu muvaffaqiyatli biriktirildi.")
                    ->success()
                    ->send();
            });
    }
}
