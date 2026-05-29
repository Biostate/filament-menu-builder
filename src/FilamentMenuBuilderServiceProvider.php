<?php

namespace Biostate\FilamentMenuBuilder;

use Biostate\FilamentMenuBuilder\Commands\FilamentMenuBuilderCommand;
use Biostate\FilamentMenuBuilder\Http\Livewire\MenuBuilder;
use Biostate\FilamentMenuBuilder\Http\Livewire\MenuItemForm;
use Biostate\FilamentMenuBuilder\Testing\TestsFilamentMenuBuilder;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMenuBuilderServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-menu-builder';

    public static string $viewNamespace = 'filament-menu-builder';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('biostate/filament-menu-builder');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function bootingPackage(): void
    {
        Livewire::component('menu-builder', MenuBuilder::class);
        Livewire::component('menu-item-form', MenuItemForm::class);

        Blade::componentNamespace('Biostate\FilamentMenuBuilder\Views', 'filament-menu-builder');
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-menu-builder/{$file->getFilename()}"),
                ], 'filament-menu-builder-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentMenuBuilder);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'biostate/filament-menu-builder';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Js::make('filament-menu-builder-scripts', __DIR__ . '/../resources/dist/filament-menu-builder.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentMenuBuilderCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        // TODO: if api enabled
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_menus_table',
            'create_menu_items_table',
            'make_menus_slug_unique',
        ];
    }
}
