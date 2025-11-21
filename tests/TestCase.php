<?php

namespace Biostate\FilamentMenuBuilder\Tests;

use Biostate\FilamentMenuBuilder\FilamentMenuBuilderServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @var \Illuminate\Testing\TestResponse|null
     */
    protected static $latestResponse;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Biostate\\FilamentMenuBuilder\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            TestPanelProvider::class,
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NestedSetServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentMenuBuilderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Run package migrations
        $migration = include __DIR__ . '/../database/migrations/create_menus_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/../database/migrations/create_menu_items_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/../database/migrations/make_menus_slug_unique.php.stub';
        $migration->up();

        // Run test migrations
        $migration = include __DIR__ . '/migrations/create_test_models_table.php';
        $migration->up();
    }

    protected function defineRoutes($router)
    {
        $router->get('/test/{model}', function ($model) {
            return "Test route for {$model}";
        })->name('test.show');

        $router->get('/home', function () {
            return 'Home page';
        })->name('home');
    }
}
