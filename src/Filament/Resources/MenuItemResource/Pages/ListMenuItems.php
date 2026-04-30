<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuItems extends ListRecords
{
    public static function getResource(): string
    {
        return FilamentMenuBuilderPlugin::get()->getMenuItemResource();
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
