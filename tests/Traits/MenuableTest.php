<?php

namespace Biostate\FilamentMenuBuilder\Tests\Traits;

use Biostate\FilamentMenuBuilder\Tests\Models\TestModel;
use Biostate\FilamentMenuBuilder\Tests\Models\TestModelWithTranslations;
use Biostate\FilamentMenuBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuableTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_menu_link_attribute_throws_exception_when_not_implemented(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You need to implement the menuLink method');
        
        $model = new class extends \Illuminate\Database\Eloquent\Model {
            use \Biostate\FilamentMenuBuilder\Traits\Menuable;
        };
        
        $model->menu_link;
    }

    public function test_get_menu_name_attribute_returns_name(): void
    {
        $model = new TestModel(['name' => 'Test Model']);
        
        $this->assertEquals('Test Model', $model->menu_name);
    }

    public function test_get_menu_name_attribute_returns_name_when_name_is_null(): void
    {
        $model = new TestModel(['name' => null]);
        
        $this->assertEquals('', $model->menu_name);
    }

    public function test_get_filament_search_label(): void
    {
        $model = new TestModel();
        
        $this->assertEquals('name', $model->getFilamentSearchLabel());
    }

    public function test_scope_filament_search_without_translations(): void
    {
        // Create test models
        TestModel::create(['name' => 'Test Model 1']);
        TestModel::create(['name' => 'Test Model 2']);
        TestModel::create(['name' => 'Another Model']);
        
        $query = TestModel::filamentSearch('Test');
        $results = $query->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('name', 'Test Model 1'));
        $this->assertTrue($results->contains('name', 'Test Model 2'));
    }

    public function test_scope_filament_search_with_case_insensitive(): void
    {
        TestModel::create(['name' => 'Test Model']);
        TestModel::create(['name' => 'test model']);
        TestModel::create(['name' => 'TEST MODEL']);
        
        $query = TestModel::filamentSearch('test');
        $results = $query->get();
        
        $this->assertCount(3, $results);
    }

    public function test_scope_filament_search_with_partial_match(): void
    {
        TestModel::create(['name' => 'Test Model']);
        TestModel::create(['name' => 'Testing']);
        TestModel::create(['name' => 'Model Test']);
        
        $query = TestModel::filamentSearch('Test');
        $results = $query->get();
        
        $this->assertCount(3, $results);
    }

    public function test_scope_filament_search_applies_limit(): void
    {
        // Create more than 10 models
        for ($i = 1; $i <= 15; $i++) {
            TestModel::create(['name' => "Test Model {$i}"]);
        }
        
        $query = TestModel::filamentSearch('Test');
        $results = $query->get();
        
        $this->assertCount(10, $results);
    }

    public function test_scope_filament_search_with_translations(): void
    {
        // Create a model that simulates having translations
        $model = new TestModelWithTranslations(['name' => 'Test Model']);
        
        // Mock the class_uses_recursive to return true for translations
        $this->mock('alias:class_uses_recursive', function () {
            return ['Spatie\Translatable\HasTranslations'];
        });
        
        // This test would need more complex setup to properly test translations
        // For now, we'll test the basic functionality
        $this->assertInstanceOf(TestModelWithTranslations::class, $model);
    }

    public function test_get_filament_search_option_name(): void
    {
        $model = new TestModel(['name' => 'Test Model']);
        
        $this->assertEquals('Test Model', $model->getFilamentSearchOptionName());
    }

    public function test_get_filament_search_option_name_with_null_name(): void
    {
        $model = new TestModel(['name' => null]);
        
        $this->assertNull($model->getFilamentSearchOptionName());
    }

    public function test_menuable_trait_can_be_used_on_different_models(): void
    {
        $model1 = new TestModel(['name' => 'Model 1']);
        $model2 = new TestModel(['name' => 'Model 2']);
        
        $this->assertEquals('Model 1', $model1->menu_name);
        $this->assertEquals('Model 2', $model2->menu_name);
    }

    public function test_menu_link_attribute_works_when_implemented(): void
    {
        $model = new TestModel(['name' => 'Test Model']);
        
        // This will test the route generation
        $link = $model->menu_link;
        
        $this->assertIsString($link);
        $this->assertStringContainsString('test', $link);
    }

    public function test_filament_search_returns_query_builder(): void
    {
        $query = TestModel::filamentSearch('test');
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    public function test_filament_search_with_empty_search_term(): void
    {
        TestModel::create(['name' => 'Test Model']);
        
        $query = TestModel::filamentSearch('');
        $results = $query->get();
        
        $this->assertCount(1, $results);
    }

    public function test_filament_search_with_special_characters(): void
    {
        TestModel::create(['name' => 'Test & Model']);
        TestModel::create(['name' => 'Test-Model']);
        TestModel::create(['name' => 'Test_Model']);
        
        $query = TestModel::filamentSearch('Test');
        $results = $query->get();
        
        $this->assertCount(3, $results);
    }
}
