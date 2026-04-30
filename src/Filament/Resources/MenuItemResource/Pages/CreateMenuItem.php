<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    public static function getResource(): string
    {
        return FilamentMenuBuilderPlugin::get()->getMenuItemResource();
    }
}
