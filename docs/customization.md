# Customization

You can extend the default Menu and MenuItem resources and models to customize their behavior. This is useful when you want to modify labels, add custom fields, or implement additional functionality.

## Using Custom Resources

In your `AdminPanelProvider.php` file, you can specify custom resource classes:

```php
use App\CustomClasses\MenuResource;
use App\CustomClasses\MenuItemResource;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configurations
        ->plugins([
            \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::make()
                ->usingMenuResource(MenuResource::class)
                ->usingMenuItemResource(MenuItemResource::class),
        ]);
}
```

## Using Custom Models

You can also specify custom model classes for Menu and MenuItem:

```php
use App\Models\CustomMenu;
use App\Models\CustomMenuItem;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configurations
        ->plugins([
            \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::make()
                ->usingMenuModel(CustomMenu::class)
                ->usingMenuItemModel(CustomMenuItem::class),
        ]);
}
```

## Creating Custom Resource Classes

Here's an example of how to extend the MenuItem resource:

```php
<?php

namespace App\CustomClasses;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource as BaseMenuItemResource;

class MenuItemResource extends BaseMenuItemResource
{
    public static function getModelLabel(): string
    {
        return 'Custom ' . parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return 'Custom ' . parent::getPluralModelLabel();
    }
}
```

You can also extend the Menu resource in a similar way:

```php
<?php

namespace App\CustomClasses;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource as BaseMenuResource;

class MenuResource extends BaseMenuResource
{
    public static function getModelLabel(): string
    {
        return 'Custom ' . parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return 'Custom ' . parent::getPluralModelLabel();
    }
}
```

## Caching

Menu items are cached in view component by default. If you want to disable caching, you can set the `cache` configuration to `false` in your config file.
