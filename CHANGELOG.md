# Changelog

All notable changes to `filament-menu-builder` will be documented in this file.

## v4.0.5 - 2025-11-21

### Added

* Dynamic model configuration support for `Menu` and `MenuItem`
* Explicit parent menu selection in Menu Item Resource
* Dynamic retrieval of Filament resource classes, enabling customized `MenuItemForm` schemas

### Testing

* Added `TestPanelProvider`
* Refactored plugin retrieval for more reliable test behavior

### Maintenance

* Updated `esbuild` to `0.25.0`
* Added `.phpunit.result.cache` to `.gitignore`
* Added missing PHPDoc type hints
* Code style cleanup
* Updated CHANGELOG

### Credits

* Merged PR #15 by @bengs: Dynamic retrieval of `MenuItemResource`

## v4.0.3 - 2025-11-22

### v4.0.3 - Dynamic Models & Resource Improvements

#### Features

- Added dynamic model configuration for Menu and MenuItem models.
- Enabled explicit parent menu selection for menu items in the resource.

#### Improvements

- Dynamically retrieve Filament resource classes from the plugin.
- Refactored plugin retrieval in `FilamentMenuBuilderPlugin`.
- Added `TestPanelProvider` for better testing infrastructure.

#### Bug Fixes

- Fixed menu item form resource access (PR #15).

## v4.0.2 - 2025-11-03

### v4.0.2 - Bug Fixes & Stability Improvements

#### Bug Fixes

- Fixed "Undefined array key 'route_parameters'" error when menu item type is 'link' (PR #12)
- Added comprehensive null safety checks throughout MenuBuilder component to prevent fatal errors
- Fixed resource resolution issues by implementing dynamic plugin-based system (PR #14)

#### Improvements

- Standardized error handling with descriptive exceptions instead of silent failures
- Added input validation for menuItemId parameter in all action methods
- Enhanced plugin validation for custom resource classes with proper type checking
- Fixed PHPStan errors and warnings, improved type safety with PHPDoc annotations

#### Architecture Changes

- Refactored resource resolution from hardcoded classes to plugin-based dynamic resolution
- Improved support for custom resource classes and multi-panel Filament setups
- Enhanced FilamentMenuBuilderPlugin with resource validation methods

#### Upgrade

```bash
composer update biostate/filament-menu-builder


```
#### New Contributors

* @kohaku1907 made their first contribution in https://github.com/Biostate/filament-menu-builder/pull/12
* @gsarigul84 made their first contribution in https://github.com/Biostate/filament-menu-builder/pull/14

**Full Changelog**: https://github.com/Biostate/filament-menu-builder/compare/v4.0.1...v4.0.2

## v4.0.1 - 2025-09-23

### What's New

* **GitHub Actions**
  
  * Fixed package test workflows.
  * Added initial basic test coverage.
  * Fixed PHPStan checks.
  
* **Error Handling**
  
  * Improved error detection to prevent `500` server errors caused by:
    
    * Route path resolution issues.
    * Empty model relations.
    
  
* **Improvements**
  
  * Made resources fully customizable.
  * Component columns are now hidden by default for cleaner output.
  

## Support Filament v4 - 2025-09-23

This version upgrades the package to fully support Filament v4.
I have tested it in my own projects, but please proceed with caution before using it in production environments.

I hope this package helps you build powerful applications!

## v1.0.9 - 2025-02-20

Improve menu slug handling:

- Fix slug regeneration issue on update.
- Ensure the menu slug is unique.
- Add a slug input field and a regenerate action in the admin panel, allowing users to update or regenerate the slug.

**Full Changelog**: https://github.com/Biostate/filament-menu-builder/compare/v1.0.8...v1.0.9

## 1.0.0 - 202X-XX-XX

- initial release
