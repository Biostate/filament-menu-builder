<?php

namespace Biostate\FilamentMenuBuilder\Tests\Models;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuItemErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_error_handling(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Route,
            'route' => 'nonexistent.route',
            'route_parameters' => collect([['key' => 'id', 'value' => '1']]),
        ]);

        $this->assertFalse($menuItem->is_route_resolved);
        $this->assertFalse($menuItem->is_link_resolved);
        $this->assertStringContainsString('Route error', $menuItem->link_error);
    }

    public function test_missing_route_parameters_error_handling(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Route,
            'route' => 'test.show', // This route requires a 'model' parameter
            'route_parameters' => collect([]), // No parameters provided
        ]);

        $this->assertFalse($menuItem->is_route_resolved);
        $this->assertFalse($menuItem->is_link_resolved);
        $this->assertStringContainsString('Missing route parameters', $menuItem->link_error);
        $this->assertNotEmpty($menuItem->missing_route_parameters);
    }

    public function test_url_error_handling(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        // Test with a URL that might cause issues
        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Link,
            'url' => null, // This should trigger URL resolution issues
        ]);

        // The resolveUrl method handles null URLs gracefully, so this should pass
        $this->assertTrue($menuItem->is_url_resolved);
        $this->assertTrue($menuItem->is_link_resolved);
        $this->assertNull($menuItem->link_error);
    }

    public function test_model_error_handling(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        // Use a valid model class but with non-existent ID
        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Model,
            'menuable_type' => \Biostate\FilamentMenuBuilder\Tests\Models\TestModel::class,
            'menuable_id' => 99999, // Non-existent ID
        ]);

        // The menuable relationship will return null for non-existent records
        $this->assertFalse($menuItem->is_link_resolved);
        $this->assertEquals('Model not found', $menuItem->link_error);
    }

    public function test_model_without_menu_link_error_handling(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        // Create a test model manually
        $testModel = new \Biostate\FilamentMenuBuilder\Tests\Models\TestModel;
        $testModel->name = 'Test Model';
        $testModel->save();

        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Model,
            'menuable_type' => get_class($testModel),
            'menuable_id' => $testModel->id,
        ]);

        // TestModel does implement menu_link, so this should work
        $this->assertTrue($menuItem->is_link_resolved);
        $this->assertNull($menuItem->link_error);
    }

    public function test_successful_link_resolution(): void
    {
        $menu = \Biostate\FilamentMenuBuilder\Models\Menu::factory()->create();

        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'type' => MenuItemType::Link,
            'url' => 'https://example.com',
        ]);

        $this->assertTrue($menuItem->is_url_resolved);
        $this->assertTrue($menuItem->is_link_resolved);
        $this->assertNull($menuItem->link_error);
    }
}
