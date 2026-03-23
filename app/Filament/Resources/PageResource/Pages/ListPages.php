<?php

namespace App\Filament\Resources\PageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\PageResource;
use App\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $count = fn(string $type) => Page::where('page_type', $type)->count();

        return [
            'all'     => Tab::make('Hammasi')
                            ->badge(Page::whereIn('page_type', ['default', 'blog'])->count()),
            'blog'    => Tab::make('Blog')
                            ->badge($count('blog'))
                            ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'blog')),
            'default' => Tab::make('Oddiy')
                            ->badge($count('default'))
                            ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'default')),
        ];
    }
}
