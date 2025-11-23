# An Elegant Menu Builder for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/biostate/filament-menu-builder.svg?style=flat-square)](https://packagist.org/packages/biostate/filament-menu-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/biostate/filament-menu-builder/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/biostate/filament-menu-builder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/biostate/filament-menu-builder/.github/workflows/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/biostate/filament-menu-builder/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/biostate/filament-menu-builder.svg?style=flat-square)](https://packagist.org/packages/biostate/filament-menu-builder)

> **Note:** If you have any suggestions, feel free to create an issue.

This package offers a powerful menu builder for the Filament admin panel, enabling efficient menu creation and management.

- Integrate models and routes into menu items for dynamic and flexible navigation.
- Render menus with Blade components for consistency and adaptability.

Built for simplicity and performance, this package ensures a seamless solution for managing menus in the Filament admin panel.

![Dark Theme](https://github.com/Biostate/filament-menu-builder/blob/main/art/configuration-dark.jpg?raw=true)
![Light Theme](https://github.com/Biostate/filament-menu-builder/blob/main/art/configuration-light.jpg?raw=true)

## 📚 Documentation

**Full documentation is available at: [https://biostate.gitbook.io/filament-menu-builder](https://biostate.gitbook.io/filament-menu-builder)**

## Version Compatibility

| Filament Version | Package Version | Composer Command |
|-----------------|-----------------|------------------|
| v3 | 1.x | `composer require biostate/filament-menu-builder:^1.0` |
| v4 | 4.x | `composer require biostate/filament-menu-builder:^4.0` |

## Quick Start

Install the package via Composer using the appropriate version from the compatibility table above:

Add the plugin to your `AdminPanelServiceProvider.php`:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::make(),
        ]);
}
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="filament-menu-builder-migrations"
php artisan migrate
```

For detailed installation instructions and configuration, see the [Installation Guide](https://biostate.gitbook.io/filament-menu-builder/installation).

## TODO

- [ ] add parameters like mega menu, dropdown, etc.
- [ ] add tests
- [ ] add tailwind blade component
- [ ] add "Do you want to discard the changes?" if you have unsaved changes
- [ ] add more actions like: move up, move down, move one level up, move one level down, etc.
- [ ] add duplicate with children action

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Süleyman Özgür Özarpacı](https://github.com/Biostate)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
