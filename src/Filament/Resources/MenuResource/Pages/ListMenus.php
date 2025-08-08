<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
