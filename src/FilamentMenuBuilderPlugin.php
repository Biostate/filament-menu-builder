<?php

namespace Biostate\FilamentMenuBuilder;

use Biostate\FilamentMenuBuilder\Contracts\MenuItemResourceInterface;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

class FilamentMenuBuilderPlugin implements Plugin
{
    protected string $menuResource = MenuResource::class;

    protected string $menuItemResource = MenuItemResource::class;

    protected string $menuModel = Menu::class;

    protected string $menuItemModel = MenuItem::class;

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
     * @return class-string<\Filament\Resources\Resource>
     */
    public function getMenuResource(): string
    {
        return $this->menuResource;
    }

    /**
     * @return class-string<\Biostate\FilamentMenuBuilder\Contracts\MenuItemResourceInterface>
     */
    public function getMenuItemResource(): string
    {
        return $this->menuItemResource;
    }

    public function usingMenuModel(string $menuModel): static
    {
        if (! class_exists($menuModel)) {
            throw new \InvalidArgumentException("Class {$menuModel} does not exist");
        }

        if (! is_subclass_of($menuModel, Model::class)) {
            throw new \InvalidArgumentException("Class {$menuModel} must extend " . Model::class);
        }

        $this->menuModel = $menuModel;

        return $this;
    }

    public function usingMenuItemModel(string $menuItemModel): static
    {
        if (! class_exists($menuItemModel)) {
            throw new \InvalidArgumentException("Class {$menuItemModel} does not exist");
        }

        if (! is_subclass_of($menuItemModel, Model::class)) {
            throw new \InvalidArgumentException("Class {$menuItemModel} must extend " . Model::class);
        }

        $this->menuItemModel = $menuItemModel;

        return $this;
    }

    public function getMenuModel(): string
    {
        return $this->menuModel;
    }

    public function getMenuItemModel(): string
    {
        return $this->menuItemModel;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament()->getPlugin(app(static::class)->getId());

        return $plugin;
    }
}
