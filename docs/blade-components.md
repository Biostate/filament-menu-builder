# Blade Components

This package provides Blade components to render menu items in your views. You can use these components in any Blade template.

## Basic Usage

The simplest way to render a menu is by using the menu slug:

```html
<x-filament-menu-builder::menu slug="main-menu" />
```

This blade component will render the menu items with the `main-menu` slug.

## Custom Views

You can also specify a custom view to render the menu. This package supports Bootstrap 5 by default:

```html
<x-filament-menu-builder::menu 
    slug="main-menu" 
    view="filament-menu-builder::components.bootstrap5.menu"
/>
```

## Publishing Views

If you want to customize the menu rendering, you can publish the views:

```bash
php artisan vendor:publish --tag="filament-menu-builder-views"
```

After publishing, you can modify the views in `resources/views/vendor/filament-menu-builder/` to match your design requirements.

## Available Views

The package comes with the following default views:

- `filament-menu-builder::components.bootstrap5.menu` - Bootstrap 5 compatible menu

You can create custom views following the same structure to support other CSS frameworks like Tailwind CSS.

## Menu Slug

The menu slug is a unique identifier for each menu. You can find the slug in the menus table in your Filament admin panel. The slug is used to identify which menu to render when using the Blade component.
