<?php

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Biostate\FilamentMenuBuilder\Models\MenuItem;

class CustomMenu extends Menu {}
class CustomMenuItem extends MenuItem {}

it('can configure dynamic models', function () {
    $plugin = FilamentMenuBuilderPlugin::make();

    $plugin->usingMenuModel(CustomMenu::class);
    $plugin->usingMenuItemModel(CustomMenuItem::class);

    expect($plugin->getMenuModel())->toBe(CustomMenu::class);
    expect($plugin->getMenuItemModel())->toBe(CustomMenuItem::class);
});

it('throws exception for invalid menu model', function () {
    $plugin = FilamentMenuBuilderPlugin::make();

    $plugin->usingMenuModel(stdClass::class);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid menu item model', function () {
    $plugin = FilamentMenuBuilderPlugin::make();

    $plugin->usingMenuItemModel(stdClass::class);
})->throws(InvalidArgumentException::class);

it('uses dynamic models in resources', function () {
    $plugin = FilamentMenuBuilderPlugin::make();
    $plugin->usingMenuModel(CustomMenu::class);
    $plugin->usingMenuItemModel(CustomMenuItem::class);

    // Mock the plugin registration in Filament
    $panel = \Filament\Facades\Filament::getCurrentPanel();
    if (! $panel) {
        // Setup a dummy panel if none exists
        $panel = \Filament\Panel::make()->id('test-panel');
        \Filament\Facades\Filament::setCurrentPanel($panel);
    }
    $panel->plugin($plugin);

    expect(\Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource::getModel())->toBe(CustomMenu::class);
    expect(\Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource::getModel())->toBe(CustomMenuItem::class);
});
