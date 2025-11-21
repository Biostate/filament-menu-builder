<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MenuBuilder extends Page
{
    use InteractsWithRecord;

    public static function getResource(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuResource();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-menu-builder::menu-builder.configure_menu');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-menu-builder::menu-builder.configure_menu');
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->heading = $this->getTitle();
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $this->record]) => $this->record->getAttribute('name'),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        return $breadcrumbs;
    }

    public function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    public function getView(): string
    {
        return 'filament-menu-builder::filament.pages.menu-builder';
    }
}
