<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    public static function getResource(): string
    {
        return FilamentMenuBuilderPlugin::get()->getMenuResource();
    }
}
