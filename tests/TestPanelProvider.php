<?php

namespace Biostate\FilamentMenuBuilder\Tests;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Panel;
use Filament\PanelProvider;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('test')
            ->plugin(FilamentMenuBuilderPlugin::make());
    }
}
