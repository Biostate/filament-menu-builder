<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
