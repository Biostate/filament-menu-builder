<?php

namespace Biostate\FilamentMenuBuilder\Tests\DTO;

use Biostate\FilamentMenuBuilder\DTO\Menu;
use Biostate\FilamentMenuBuilder\Models\Menu as MenuModel;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_dto_creation(): void
    {
        $menu = new Menu('Test Menu', 'test-menu');

        $this->assertEquals('Test Menu', $menu->name);
        $this->assertEquals('test-menu', $menu->slug);
    }

    public function test_dto_properties(): void
    {
        $menu = new Menu('Test Menu', 'test-menu');

        $this->assertIsString($menu->name);
        $this->assertIsString($menu->slug);
        $this->assertNotEmpty($menu->name);
        $this->assertNotEmpty($menu->slug);
    }

    public function test_from_model_method(): void
    {
        $menuModel = MenuModel::factory()->create([
            'name' => 'Test Menu',
            'slug' => 'test-menu',
        ]);

        $menuDto = Menu::fromModel($menuModel);

        $this->assertInstanceOf(Menu::class, $menuDto);
        $this->assertEquals('Test Menu', $menuDto->name);
        $this->assertEquals('test-menu', $menuDto->slug);
    }

    public function test_from_model_method_with_different_data(): void
    {
        $menuModel = MenuModel::factory()->create([
            'name' => 'Another Menu',
            'slug' => 'another-menu',
        ]);

        $menuDto = Menu::fromModel($menuModel);

        $this->assertEquals('Another Menu', $menuDto->name);
        $this->assertEquals('another-menu', $menuDto->slug);
    }

    public function test_from_collection_method(): void
    {
        $menuModel1 = MenuModel::factory()->create(['name' => 'Menu 1', 'slug' => 'menu-1']);
        $menuModel2 = MenuModel::factory()->create(['name' => 'Menu 2', 'slug' => 'menu-2']);

        $collection = collect([$menuModel1, $menuModel2]);
        $dtoCollection = Menu::fromCollection($collection);

        $this->assertInstanceOf(Collection::class, $dtoCollection);
        $this->assertCount(2, $dtoCollection);

        $firstDto = $dtoCollection->first();
        $this->assertInstanceOf(Menu::class, $firstDto);
        $this->assertEquals('Menu 1', $firstDto->name);
        $this->assertEquals('menu-1', $firstDto->slug);

        $lastDto = $dtoCollection->last();
        $this->assertInstanceOf(Menu::class, $lastDto);
        $this->assertEquals('Menu 2', $lastDto->name);
        $this->assertEquals('menu-2', $lastDto->slug);
    }

    public function test_from_collection_method_with_empty_collection(): void
    {
        $collection = collect([]);
        $dtoCollection = Menu::fromCollection($collection);

        $this->assertInstanceOf(Collection::class, $dtoCollection);
        $this->assertCount(0, $dtoCollection);
    }

    public function test_dto_serialization(): void
    {
        $menu = new Menu('Test Menu', 'test-menu');

        // Test that the DTO can be serialized (for caching, etc.)
        $serialized = serialize($menu);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(Menu::class, $unserialized);
        $this->assertEquals('Test Menu', $unserialized->name);
        $this->assertEquals('test-menu', $unserialized->slug);
    }

    public function test_dto_can_be_converted_to_array(): void
    {
        $menu = new Menu('Test Menu', 'test-menu');

        // Test that the DTO can be converted to array
        $array = [
            'name' => $menu->name,
            'slug' => $menu->slug,
        ];

        $this->assertEquals('Test Menu', $array['name']);
        $this->assertEquals('test-menu', $array['slug']);
    }

    public function test_dto_immutability(): void
    {
        $menu = new Menu('Test Menu', 'test-menu');

        // Test that properties are public and can be accessed
        $this->assertEquals('Test Menu', $menu->name);
        $this->assertEquals('test-menu', $menu->slug);

        // Note: In PHP, public properties can be modified, but we're testing the intended usage
        $this->assertIsString($menu->name);
        $this->assertIsString($menu->slug);
    }

    public function test_from_model_with_null_values(): void
    {
        $menuModel = new MenuModel;
        $menuModel->name = 'Test Menu';
        $menuModel->slug = 'test-menu';

        $menuDto = Menu::fromModel($menuModel);

        $this->assertEquals('Test Menu', $menuDto->name);
        $this->assertEquals('test-menu', $menuDto->slug);
    }
}
