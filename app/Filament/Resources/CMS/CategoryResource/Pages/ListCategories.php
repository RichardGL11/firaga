<?php

namespace App\Filament\Resources\CMS\CategoryResource\Pages;

use App\Filament\Resources\CMS\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webid\Druid\Facades\Druid;
use Webid\Druid\Repositories\CategoryRepository;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        if (! Druid::isMultilingualEnabled()) {
            return [];
        }

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = app(CategoryRepository::class);

        $tabs = [
            'all' => Tab::make(__('All'))->icon('heroicon-s-flag')
                ->badge($categoryRepository->countAll()),
        ];

        foreach (Druid::getLocales() as $localeKey => $localeData) {
            $tabs[$localeKey] = Tab::make($localeData['label'])
                ->modifyQueryUsing(fn (Builder $query) => $query->where('lang', $localeKey))
                ->badge($categoryRepository->countAllHavingLang($localeKey));
        }

        $noLangMenuCount = $categoryRepository->countAllWithoutLang();
        if ($noLangMenuCount > 0) {
            $tabs['no-lang'] = Tab::make(__('No lang'))->icon('heroicon-m-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('lang'))
                ->badge($noLangMenuCount);
        }

        return $tabs;
    }
}
