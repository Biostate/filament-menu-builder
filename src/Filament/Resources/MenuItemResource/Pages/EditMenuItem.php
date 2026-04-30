<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    public static function getResource(): string
    {
        return FilamentMenuBuilderPlugin::get()->getMenuItemResource();
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
