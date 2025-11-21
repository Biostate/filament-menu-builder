<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    public static function getResource(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuResource();
    }
}
