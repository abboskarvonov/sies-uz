<?php

namespace App\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class SyncPageContentBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'syncPageContent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label("Kontent va rasmni ko'chirish")
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->modalHeading("Sahifadan sahifaga kontent ko'chirish")
            ->deselectRecordsAfterCompletion()
            ->form(function (Collection $records): array {
                if ($records->count() !== 2) {
                    return [
                        Placeholder::make('warning')
                            ->label('')
                            ->content(
                                "⚠️ Ushbu amal faqat 2 ta yozuv tanlanganda ishlaydi. " .
                                "Hozir {$records->count()} ta tanlangan. Bekor qiling va qayta urining."
                            ),
                    ];
                }

                $options = $records->mapWithKeys(function ($record) {
                    $badge = $record->hemis_id
                        ? '<span style="display:inline-flex;align-items:center;gap:4px;color:#16a34a;font-weight:600;">'
                          . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>'
                          . 'HEMIS</span>'
                        : '<span style="display:inline-flex;align-items:center;gap:4px;color:#9ca3af;">'
                          . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.885L17.5 5.5a2.121 2.121 0 0 0-3-3L3.58 13.42a4 4 0 0 0-.885 1.343Z"/></svg>'
                          . "Qo'lda</span>";

                    return [
                        $record->id => $record->title_uz . ' &nbsp;' . $badge,
                    ];
                })->toArray();

                return [
                    Select::make('source_id')
                        ->label("Manba sahifasi (qayerdan ko'chiriladi)")
                        ->options($options)
                        ->allowHtml()
                        ->required()
                        ->helperText("Yashil ✓ HEMIS = HEMIS dan yuklangan | Kulrang ✏ Qo'lda = qo'lda kiritilgan."),

                    CheckboxList::make('fields')
                        ->label("Nimalar ko'chirilsin?")
                        ->options([
                            'content' => 'Kontent (uz / ru / en)',
                            'image'   => 'Asosiy rasm',
                            'images'  => 'Galereya rasmlari',
                            'views'   => "Ko'rishlar soni (views)",
                        ])
                        ->default(['content', 'image', 'images', 'views'])
                        ->columns(2)
                        ->required(),
                ];
            })
            ->action(function (Collection $records, array $data): void {
                if ($records->count() !== 2) {
                    Notification::make()
                        ->title('Xato')
                        ->body("Faqat 2 ta yozuv tanlanishi kerak.")
                        ->danger()
                        ->send();
                    return;
                }

                $fields = $data['fields'] ?? [];

                if (empty($fields)) {
                    Notification::make()
                        ->title("Hech narsa tanlanmadi")
                        ->body("Ko'chiriladigan maydon tanlanmadi.")
                        ->warning()
                        ->send();
                    return;
                }

                $sourceId = (int) $data['source_id'];
                $source   = $records->firstWhere('id', $sourceId);
                $target   = $records->first(fn($r) => $r->id !== $sourceId);

                if (! $source || ! $target) {
                    Notification::make()
                        ->title('Xato')
                        ->body("Manba yoki maqsad sahifasi topilmadi.")
                        ->danger()
                        ->send();
                    return;
                }

                $update = [];

                if (in_array('content', $fields)) {
                    $update['content_uz'] = $source->content_uz;
                    $update['content_ru'] = $source->content_ru;
                    $update['content_en'] = $source->content_en;
                }

                if (in_array('image', $fields)) {
                    $update['image'] = $source->image;
                }

                if (in_array('images', $fields)) {
                    $update['images'] = $source->images;
                }

                if (in_array('views', $fields)) {
                    $update['views'] = $source->views;
                }

                $target->update($update);

                $fieldLabels = array_filter([
                    in_array('content', $fields) ? 'kontent' : null,
                    in_array('image', $fields)   ? 'rasm' : null,
                    in_array('images', $fields)  ? 'galereya' : null,
                    in_array('views', $fields)   ? "ko'rishlar soni" : null,
                ]);

                Notification::make()
                    ->title("Muvaffaqiyatli ko'chirildi")
                    ->body(
                        "\"{$source->title_uz}\" dan \"{$target->title_uz}\" ga " .
                        implode(', ', $fieldLabels) . " ko'chirildi."
                    )
                    ->success()
                    ->send();
            });
    }
}
