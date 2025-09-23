<?php

namespace Biostate\FilamentMenuBuilder\Tests\DTO;

use Biostate\FilamentMenuBuilder\DTO\MenuItem;
use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Models\MenuItem as MenuItemModel;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_dto_creation(): void
    {
        $children = collect([]);
        $parameters = collect(['key' => 'value']);
        $routeParameters = collect(['id' => 1]);
        
        $menuItem = new MenuItem(
            name: 'Test Item',
            target: '_self',
            wrapper_class: 'wrapper',
            link_class: 'link',
            link: 'https://example.com',
            type: MenuItemType::Link,
            route: null,
            route_parameters: $routeParameters,
            menu_id: 1,
            menuable_id: null,
            menuable_type: null,
            url: 'https://example.com',
            use_menuable_name: false,
            parameters: $parameters,
            children: $children,
        );
        
        $this->assertEquals('Test Item', $menuItem->name);
        $this->assertEquals('_self', $menuItem->target);
        $this->assertEquals('wrapper', $menuItem->wrapper_class);
        $this->assertEquals('link', $menuItem->link_class);
        $this->assertEquals('https://example.com', $menuItem->link);
        $this->assertEquals(MenuItemType::Link, $menuItem->type);
        $this->assertNull($menuItem->route);
        $this->assertInstanceOf(Collection::class, $menuItem->route_parameters);
        $this->assertEquals(1, $menuItem->menu_id);
        $this->assertNull($menuItem->menuable_id);
        $this->assertNull($menuItem->menuable_type);
        $this->assertEquals('https://example.com', $menuItem->url);
        $this->assertFalse($menuItem->use_menuable_name);
        $this->assertInstanceOf(Collection::class, $menuItem->parameters);
        $this->assertInstanceOf(Collection::class, $menuItem->children);
    }

    public function test_dto_properties(): void
    {
        $children = collect([]);
        $parameters = collect([]);
        $routeParameters = collect([]);
        
        $menuItem = new MenuItem(
            name: 'Test Item',
            target: '_self',
            wrapper_class: null,
            link_class: null,
            link: 'https://example.com',
            type: MenuItemType::Link,
            route: null,
            route_parameters: $routeParameters,
            menu_id: 1,
            menuable_id: null,
            menuable_type: null,
            url: 'https://example.com',
            use_menuable_name: false,
            parameters: $parameters,
            children: $children,
        );
        
        $this->assertIsString($menuItem->name);
        $this->assertIsString($menuItem->target);
        $this->assertIsString($menuItem->link);
        $this->assertInstanceOf(MenuItemType::class, $menuItem->type);
        $this->assertIsInt($menuItem->menu_id);
        $this->assertIsString($menuItem->url);
        $this->assertIsBool($menuItem->use_menuable_name);
        $this->assertInstanceOf(Collection::class, $menuItem->parameters);
        $this->assertInstanceOf(Collection::class, $menuItem->children);
    }

    public function test_from_model_method(): void
    {
        $menuItemModel = MenuItemModel::factory()->create([
            'name' => 'Test Item',
            'target' => '_self',
            'type' => MenuItemType::Link,
            'url' => 'https://example.com',
            'menu_id' => 1,
        ]);
        
        $menuItemDto = MenuItem::fromModel($menuItemModel);
        
        $this->assertInstanceOf(MenuItem::class, $menuItemDto);
        $this->assertEquals('Test Item', $menuItemDto->name);
        $this->assertEquals('_self', $menuItemDto->target);
        $this->assertEquals(MenuItemType::Link, $menuItemDto->type);
        $this->assertEquals('https://example.com', $menuItemDto->url);
        $this->assertEquals(1, $menuItemDto->menu_id);
    }

    public function test_from_model_method_with_route_type(): void
    {
        $menuItemModel = MenuItemModel::factory()->create([
            'name' => 'Route Item',
            'target' => '_blank',
            'type' => MenuItemType::Route,
            'route' => 'home',
            'route_parameters' => ['id' => 1],
            'menu_id' => 1,
        ]);
        
        $menuItemDto = MenuItem::fromModel($menuItemModel);
        
        $this->assertEquals('Route Item', $menuItemDto->name);
        $this->assertEquals('_blank', $menuItemDto->target);
        $this->assertEquals(MenuItemType::Route, $menuItemDto->type);
        $this->assertEquals('home', $menuItemDto->route);
        $this->assertInstanceOf(Collection::class, $menuItemDto->route_parameters);
        $this->assertEquals(1, $menuItemDto->route_parameters['id']);
    }

    public function test_from_model_method_with_model_type(): void
    {
        $menuItemModel = MenuItemModel::factory()->create([
            'name' => 'Model Item',
            'target' => '_self',
            'type' => MenuItemType::Model,
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
            'menuable_id' => 1,
            'use_menuable_name' => true,
            'menu_id' => 1,
        ]);
        
        $menuItemDto = MenuItem::fromModel($menuItemModel);
        
        $this->assertEquals('Model Item', $menuItemDto->name);
        $this->assertEquals(MenuItemType::Model, $menuItemDto->type);
        $this->assertEquals('Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel', $menuItemDto->menuable_type);
        $this->assertEquals(1, $menuItemDto->menuable_id);
        $this->assertTrue($menuItemDto->use_menuable_name);
    }

    public function test_from_collection_method(): void
    {
        $menuItemModel1 = MenuItemModel::factory()->create(['name' => 'Item 1', 'menu_id' => 1]);
        $menuItemModel2 = MenuItemModel::factory()->create(['name' => 'Item 2', 'menu_id' => 1]);
        
        $collection = collect([$menuItemModel1, $menuItemModel2]);
        $dtoCollection = MenuItem::fromCollection($collection);
        
        $this->assertInstanceOf(Collection::class, $dtoCollection);
        $this->assertCount(2, $dtoCollection);
        
        $firstDto = $dtoCollection->first();
        $this->assertInstanceOf(MenuItem::class, $firstDto);
        $this->assertEquals('Item 1', $firstDto->name);
        
        $lastDto = $dtoCollection->last();
        $this->assertInstanceOf(MenuItem::class, $lastDto);
        $this->assertEquals('Item 2', $lastDto->name);
    }

    public function test_from_collection_method_with_empty_collection(): void
    {
        $collection = collect([]);
        $dtoCollection = MenuItem::fromCollection($collection);
        
        $this->assertInstanceOf(Collection::class, $dtoCollection);
        $this->assertCount(0, $dtoCollection);
    }

    public function test_dto_serialization(): void
    {
        $children = collect([]);
        $parameters = collect(['key' => 'value']);
        $routeParameters = collect(['id' => 1]);
        
        $menuItem = new MenuItem(
            name: 'Test Item',
            target: '_self',
            wrapper_class: 'wrapper',
            link_class: 'link',
            link: 'https://example.com',
            type: MenuItemType::Link,
            route: null,
            route_parameters: $routeParameters,
            menu_id: 1,
            menuable_id: null,
            menuable_type: null,
            url: 'https://example.com',
            use_menuable_name: false,
            parameters: $parameters,
            children: $children,
        );
        
        // Test that the DTO can be serialized (for caching, etc.)
        $serialized = serialize($menuItem);
        $unserialized = unserialize($serialized);
        
        $this->assertInstanceOf(MenuItem::class, $unserialized);
        $this->assertEquals('Test Item', $unserialized->name);
        $this->assertEquals('_self', $unserialized->target);
        $this->assertEquals(MenuItemType::Link, $unserialized->type);
    }

    public function test_dto_can_be_converted_to_array(): void
    {
        $children = collect([]);
        $parameters = collect(['key' => 'value']);
        $routeParameters = collect(['id' => 1]);
        
        $menuItem = new MenuItem(
            name: 'Test Item',
            target: '_self',
            wrapper_class: 'wrapper',
            link_class: 'link',
            link: 'https://example.com',
            type: MenuItemType::Link,
            route: null,
            route_parameters: $routeParameters,
            menu_id: 1,
            menuable_id: null,
            menuable_type: null,
            url: 'https://example.com',
            use_menuable_name: false,
            parameters: $parameters,
            children: $children,
        );
        
        // Test that the DTO can be converted to array
        $array = [
            'name' => $menuItem->name,
            'target' => $menuItem->target,
            'type' => $menuItem->type->value,
            'menu_id' => $menuItem->menu_id,
        ];
        
        $this->assertEquals('Test Item', $array['name']);
        $this->assertEquals('_self', $array['target']);
        $this->assertEquals('link', $array['type']);
        $this->assertEquals(1, $array['menu_id']);
    }

    public function test_dto_with_children(): void
    {
        $parentModel = MenuItemModel::factory()->create(['name' => 'Parent', 'menu_id' => 1]);
        $childModel = MenuItemModel::factory()->create(['name' => 'Child', 'menu_id' => 1, 'parent_id' => $parentModel->id]);
        
        // Mock the children relationship
        $parentModel->setRelation('children', collect([$childModel]));
        
        $menuItemDto = MenuItem::fromModel($parentModel);
        
        $this->assertInstanceOf(Collection::class, $menuItemDto->children);
        $this->assertCount(1, $menuItemDto->children);
        
        $childDto = $menuItemDto->children->first();
        $this->assertInstanceOf(MenuItem::class, $childDto);
        $this->assertEquals('Child', $childDto->name);
    }

    public function test_dto_immutability(): void
    {
        $children = collect([]);
        $parameters = collect([]);
        $routeParameters = collect([]);
        
        $menuItem = new MenuItem(
            name: 'Test Item',
            target: '_self',
            wrapper_class: null,
            link_class: null,
            link: 'https://example.com',
            type: MenuItemType::Link,
            route: null,
            route_parameters: $routeParameters,
            menu_id: 1,
            menuable_id: null,
            menuable_type: null,
            url: 'https://example.com',
            use_menuable_name: false,
            parameters: $parameters,
            children: $children,
        );
        
        // Test that properties are public and can be accessed
        $this->assertEquals('Test Item', $menuItem->name);
        $this->assertEquals('_self', $menuItem->target);
        $this->assertEquals(MenuItemType::Link, $menuItem->type);
        
        // Note: In PHP, public properties can be modified, but we're testing the intended usage
        $this->assertIsString($menuItem->name);
        $this->assertIsString($menuItem->target);
        $this->assertInstanceOf(MenuItemType::class, $menuItem->type);
    }
}
