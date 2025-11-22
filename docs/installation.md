# Installation

## Version Compatibility

| Filament Version | Package Version | Composer Command |
|-----------------|-----------------|------------------|
| v3 | 1.x | `composer require biostate/filament-menu-builder:^1.0` |
| v4 | 4.x | `composer require biostate/filament-menu-builder:^4.0` |

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Filament 3.0 or higher (for version 1.x)
- Filament 4.0 or higher (for version 4.x)

## Step 1: Install via Composer

Install the package using the appropriate command from the compatibility table above, or use:

```bash
composer require biostate/filament-menu-builder
```

## Step 2: Add Plugin to Filament Panel

Add the plugin to your `AdminPanelServiceProvider.php`:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        // Your other configurations
        ->plugins([
            \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::make(), // Add this line
        ]);
}
```

## Step 3: Publish and Run Migrations

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-menu-builder-migrations"
php artisan migrate
```

## Step 4: Publish Config File (Optional)

You can publish the config file to customize the package behavior:

```bash
php artisan vendor:publish --tag="filament-menu-builder-config"
```

## Step 5: Publish Views (Optional)

Optionally, you can publish the views for customization:

```bash
php artisan vendor:publish --tag="filament-menu-builder-views"
```

## Step 6: Configure Custom Theme

This package requires a custom theme that Filament provides.

1. **Create a custom theme** following the [Filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme).

2. **Add the package's views to your theme** by including this in your generated `theme.css`:

```css
@source '../../../../vendor/biostate/filament-menu-builder/resources/views/**/*';
```

3. Run this command in your project root:

```bash
npm run build
```

## Next Steps

- Learn about [Customization](customization.md) to extend the package
- Check out the [Menuable Trait](menuable-trait.md) to link models
- See [Blade Components](blade-components.md) for rendering menus
