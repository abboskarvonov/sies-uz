<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $count = fn(string $type) => Page::where('page_type', $type)->count();

        return [
            'all'        => Tab::make('Hammasi')
                               ->badge(Page::count()),
            'blog'       => Tab::make('Blog')
                               ->badge($count('blog'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'blog')),
            'default'    => Tab::make('Oddiy')
                               ->badge($count('default'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'default')),
            'faculty'    => Tab::make('Fakultet')
                               ->badge($count('faculty'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'faculty')),
            'department' => Tab::make('Kafedra')
                               ->badge($count('department'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'department')),
            'center'     => Tab::make('Markaz')
                               ->badge($count('center'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'center')),
            'section'    => Tab::make('Bo\'lim')
                               ->badge($count('section'))
                               ->modifyQueryUsing(fn(Builder $query) => $query->where('page_type', 'section')),
        ];
    }
}
