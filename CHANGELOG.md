# Changelog

All notable changes to `filament-menu-builder` will be documented in this file.

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
