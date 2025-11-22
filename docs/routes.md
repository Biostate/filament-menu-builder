# Routes

You can use your application routes in the menu items. The package automatically discovers all available routes in your application.

## Default Route Exclusion

By default, the package excludes certain routes to keep the menu item selection clean:

- Debugbar routes (`/^debugbar\./`)
- Filament routes (`/^filament\./`)
- Livewire routes (`/^livewire\./`)

## Custom Route Exclusion

If you want to exclude additional routes, you can configure the `exclude_route_names` option in your config file:

```php
return [
    'exclude_route_names' => [
        '/^debugbar\./', // Exclude debugbar routes
        '/^filament\./',   // Exclude filament routes
        '/^livewire\./',   // Exclude livewire routes
        '/^api\./',        // Exclude API routes
        '/^admin\./',      // Exclude admin routes
    ],
];
```

The exclusion patterns use regular expressions, so you can create flexible matching rules.

## Using Routes in Menu Items

When creating or editing menu items in the Filament admin panel, you'll see a dropdown of available routes. Simply select the route you want to use, and the package will automatically generate the correct URL.
