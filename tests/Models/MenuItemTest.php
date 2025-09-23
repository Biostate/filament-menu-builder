<?php

namespace Biostate\FilamentMenuBuilder\Tests\Models;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $menuItem = new MenuItem();
        $fillable = $menuItem->getFillable();
        
        $expectedFillable = [
            'id',
            'name',
            'target',
            'type',
            'route',
            'route_parameters',
            'link_class',
            'wrapper_class',
            'menu_id',
            'parameters',
            'menuable_id',
            'menuable_type',
            'url',
            'use_menuable_name',
        ];
        
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable);
        }
    }

    public function test_casts_configuration(): void
    {
        $menuItem = new MenuItem();
        $casts = $menuItem->getCasts();
        
        $this->assertEquals('collection', $casts['parameters']);
        $this->assertEquals('collection', $casts['route_parameters']);
        $this->assertEquals(MenuItemType::class, $casts['type']);
    }

    public function test_menu_relationship(): void
    {
        $menu = Menu::factory()->create();
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);
        
        $this->assertInstanceOf(Menu::class, $menuItem->menu);
        $this->assertEquals($menu->id, $menuItem->menu->id);
    }

    public function test_menuable_morph_to_relationship(): void
    {
        $menuItem = MenuItem::factory()->create([
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
            'menuable_id' => 1,
        ]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $menuItem->menuable());
    }

    public function test_get_menu_name_attribute_without_menuable(): void
    {
        $menuItem = MenuItem::factory()->create(['name' => 'Test Item']);
        
        $this->assertEquals('Test Item', $menuItem->menu_name);
    }

    public function test_get_menu_name_attribute_with_menuable_and_use_menuable_name(): void
    {
        $menuItem = MenuItem::factory()->create([
            'name' => 'Original Name',
            'type' => MenuItemType::Model,
            'use_menuable_name' => true,
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
            'menuable_id' => 1,
        ]);
        
        // Since we don't have a real menuable model, it should fall back to original name
        $this->assertEquals('Original Name', $menuItem->menu_name);
    }

    public function test_get_normalized_type_attribute_for_non_model_type(): void
    {
        $menuItem = MenuItem::factory()->create(['type' => MenuItemType::Link]);
        
        $this->assertEquals('Link', $menuItem->normalized_type);
    }

    public function test_get_normalized_type_attribute_for_model_type(): void
    {
        $menuItem = MenuItem::factory()->create([
            'type' => MenuItemType::Model,
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
        ]);
        
        $this->assertEquals('TestModel', $menuItem->normalized_type);
    }

    public function test_get_link_attribute_for_link_type(): void
    {
        $menuItem = MenuItem::factory()->create([
            'type' => MenuItemType::Link,
            'url' => 'https://example.com',
        ]);
        
        $this->assertEquals('https://example.com', $menuItem->link);
    }

    public function test_get_link_attribute_for_route_type(): void
    {
        $menuItem = MenuItem::factory()->create([
            'type' => MenuItemType::Route,
            'route' => 'home',
            'route_parameters' => ['id' => 1],
        ]);
        
        // This will test the route generation, but may fail if route doesn't exist
        $this->assertIsString($menuItem->link);
    }

    public function test_get_link_attribute_for_model_type(): void
    {
        $menuItem = MenuItem::factory()->create([
            'type' => MenuItemType::Model,
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
            'menuable_id' => 1,
        ]);
        
        // Since we don't have a real menuable model, it should return '#'
        $this->assertEquals('#', $menuItem->link);
    }

    public function test_resolve_url_method_with_valid_url(): void
    {
        $menuItem = MenuItem::factory()->create(['url' => 'https://example.com']);
        
        $this->assertEquals('https://example.com', $menuItem->resolveUrl());
    }

    public function test_resolve_url_method_with_relative_url(): void
    {
        $menuItem = MenuItem::factory()->create(['url' => '/about']);
        
        $this->assertEquals(url('/about'), $menuItem->resolveUrl());
    }

    public function test_resolve_url_method_with_hash(): void
    {
        $menuItem = MenuItem::factory()->create(['url' => '#']);
        
        $this->assertEquals('#', $menuItem->resolveUrl());
    }

    public function test_resolve_url_method_with_null_url(): void
    {
        $menuItem = MenuItem::factory()->create(['url' => null]);
        
        $this->assertEquals(url('/'), $menuItem->resolveUrl());
    }

    public function test_nested_set_functionality(): void
    {
        $menu = Menu::factory()->create();
        $parentItem = MenuItem::factory()->create(['menu_id' => $menu->id]);
        $childItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parentItem->id,
        ]);
        
        $this->assertEquals($parentItem->id, $childItem->parent_id);
    }

    public function test_timestamps_disabled(): void
    {
        $menuItem = MenuItem::factory()->create();
        
        $this->assertFalse($menuItem->timestamps);
    }

    public function test_touches_menu_on_update(): void
    {
        $menu = Menu::factory()->create();
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);
        
        $originalUpdatedAt = $menu->updated_at;
        
        // Wait a moment to ensure timestamp difference
        sleep(1);
        
        // Update the menu item
        $menuItem->update(['name' => 'Updated Name']);
        
        // The menu should be touched (updated_at should change)
        $this->assertNotEquals($originalUpdatedAt, $menu->fresh()->updated_at);
    }

    public function test_parameters_cast_to_collection(): void
    {
        $menuItem = MenuItem::factory()->create([
            'parameters' => ['key1' => 'value1', 'key2' => 'value2'],
        ]);
        
        $this->assertInstanceOf(Collection::class, $menuItem->parameters);
        $this->assertEquals('value1', $menuItem->parameters['key1']);
        $this->assertEquals('value2', $menuItem->parameters['key2']);
    }

    public function test_route_parameters_cast_to_collection(): void
    {
        $menuItem = MenuItem::factory()->create([
            'route_parameters' => ['id' => 1, 'slug' => 'test'],
        ]);
        
        $this->assertInstanceOf(Collection::class, $menuItem->route_parameters);
        $this->assertEquals(1, $menuItem->route_parameters['id']);
        $this->assertEquals('test', $menuItem->route_parameters['slug']);
    }

    public function test_factory_creation(): void
    {
        $menuItem = MenuItem::factory()->create();
        
        $this->assertInstanceOf(MenuItem::class, $menuItem);
        $this->assertNotNull($menuItem->name);
        $this->assertNotNull($menuItem->type);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
        ]);
    }

    public function test_can_create_menu_item_with_all_fields(): void
    {
        $menu = Menu::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'name' => 'Test Item',
            'target' => '_blank',
            'type' => MenuItemType::Link,
            'url' => 'https://example.com',
            'link_class' => 'custom-link',
            'wrapper_class' => 'custom-wrapper',
            'parameters' => ['custom' => 'param'],
        ]);
        
        $this->assertEquals('Test Item', $menuItem->name);
        $this->assertEquals('_blank', $menuItem->target);
        $this->assertEquals(MenuItemType::Link, $menuItem->type);
        $this->assertEquals('https://example.com', $menuItem->url);
        $this->assertEquals('custom-link', $menuItem->link_class);
        $this->assertEquals('custom-wrapper', $menuItem->wrapper_class);
        $this->assertEquals('param', $menuItem->parameters['custom']);
    }
}
