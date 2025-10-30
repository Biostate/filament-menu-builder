<?php

namespace Biostate\FilamentMenuBuilder;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentMenuBuilderPlugin implements Plugin
{
    protected string $menuResource = MenuResource::class;

    protected string $menuItemResource = MenuItemResource::class;

    public function getId(): string
    {
        return 'filament-menu-builder';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            $this->menuResource,
            $this->menuItemResource,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function usingMenuResource(string $menuResource): static
    {
        $this->menuResource = $menuResource;

        return $this;
    }

    public function usingMenuItemResource(string $menuItemResource): static
    {
        $this->menuItemResource = $menuItemResource;

        return $this;
    }

    public function getMenuResource(): string
    {
        return $this->menuResource;
    }

    public function getMenuItemResource(): string
    {
        return $this->menuItemResource;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
