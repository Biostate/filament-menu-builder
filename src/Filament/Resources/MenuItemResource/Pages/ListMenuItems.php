<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
