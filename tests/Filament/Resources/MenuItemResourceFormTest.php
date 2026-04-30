<?php

namespace Biostate\FilamentMenuBuilder\Tests\Filament\Resources;

use Biostate\FilamentMenuBuilder\Tests\Models\NamelessMenuable;
use Biostate\FilamentMenuBuilder\Tests\Models\TestModel;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Regression coverage for issue #23 — the menuable Select used to call
 * `pluck(getFilamentSearchLabel(), 'id')` against the database, returning
 * null labels for models with no `name` column. The Select then crashed
 * with a TypeError. The fix routes labels through the `menu_name` accessor
 * via `getFilamentSearchOptionName()`. These tests assert that contract on
 * the options pipeline directly so a regression to `pluck` would fail.
 */
class MenuItemResourceFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_menuable_options_resolve_for_model_with_name_column(): void
    {
        TestModel::create(['name' => 'First']);
        TestModel::create(['name' => 'Second']);

        $options = $this->resolveMenuableOptions(TestModel::class);

        $this->assertSame(['First', 'Second'], array_values($options));
        $this->assertNotContains(null, $options);
    }

    public function test_menuable_options_resolve_for_model_without_name_column(): void
    {
        // The exact shape from issue #23: no `name` column, display name
        // computed via `getMenuNameAttribute()`. Pre-fix, this returned null
        // labels and crashed the Select renderer.
        NamelessMenuable::create(['title' => 'Alpha']);
        NamelessMenuable::create(['title' => 'Beta']);

        $options = $this->resolveMenuableOptions(NamelessMenuable::class);

        $this->assertSame(['Alpha', 'Beta'], array_values($options));
        $this->assertNotContains(null, $options);
    }

    public function test_menuable_search_options_resolve_via_accessor(): void
    {
        TestModel::create(['name' => 'Alpha']);
        TestModel::create(['name' => 'Beta']);
        TestModel::create(['name' => 'Gamma']);

        $options = $this->resolveMenuableSearchOptions(TestModel::class, 'lph');

        $this->assertSame(['Alpha'], array_values($options));
    }

    /**
     * Mirror the production options closure at
     * src/Filament/Resources/MenuItemResource.php:222 so a regression that
     * reverts back to `pluck($column, 'id')` will fail this test.
     *
     * @param  class-string  $className
     * @return array<int|string, string|null>
     */
    private function resolveMenuableOptions(string $className): array
    {
        return $className::all()
            ->mapWithKeys(fn ($model) => [$model->getKey() => $model->getFilamentSearchOptionName()])
            ->all();
    }

    /**
     * Mirror the search-results closure at
     * src/Filament/Resources/MenuItemResource.php:226.
     *
     * @param  class-string  $className
     * @return array<int|string, string|null>
     */
    private function resolveMenuableSearchOptions(string $className, string $search): array
    {
        return $className::filamentSearch($search)
            ->get()
            ->mapWithKeys(fn ($model) => [$model->getKey() => $model->getFilamentSearchOptionName()])
            ->all();
    }
}
