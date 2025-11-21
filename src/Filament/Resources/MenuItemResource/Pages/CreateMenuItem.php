<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    public static function getResource(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuItemResource();
    }
}
