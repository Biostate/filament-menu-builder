<?php

namespace Biostate\FilamentMenuBuilder;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Resource;

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
        if (! class_exists($menuResource)) {
            throw new \InvalidArgumentException("Class {$menuResource} does not exist");
        }

        if (! is_subclass_of($menuResource, Resource::class)) {
            throw new \InvalidArgumentException("Class {$menuResource} must extend " . Resource::class);
        }

        $this->menuResource = $menuResource;

        return $this;
    }

    public function usingMenuItemResource(string $menuItemResource): static
    {
        if (! class_exists($menuItemResource)) {
            throw new \InvalidArgumentException("Class {$menuItemResource} does not exist");
        }

        if (! is_subclass_of($menuItemResource, Resource::class)) {
            throw new \InvalidArgumentException("Class {$menuItemResource} must extend " . Resource::class);
        }

        $this->menuItemResource = $menuItemResource;

        return $this;
    }

    /**
     * @return class-string<resource>
     */
    public function getMenuResource(): string
    {
        return $this->menuResource;
    }

    /**
     * @return class-string<resource>
     */
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
