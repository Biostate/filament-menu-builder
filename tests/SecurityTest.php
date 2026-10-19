<?php

namespace Biostate\FilamentMenuBuilder\Tests;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Http\Livewire\MenuBuilder;
use Biostate\FilamentMenuBuilder\Http\Livewire\MenuItemForm;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Biostate\FilamentMenuBuilder\Tests\Models\TestModel;
use Biostate\FilamentMenuBuilder\Tests\Models\TestModelWithTranslations;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));

        Filament::setCurrentPanel(Filament::getPanel('test'));
    }

    private function makeItem(Menu $menu, string $name = 'Item'): MenuItem
    {
        return MenuItem::create([
            'name' => $name,
            'type' => MenuItemType::Link,
            'url' => '/page',
            'target' => '_self',
            'menu_id' => $menu->id,
        ]);
    }

    public function test_script_urls_are_not_rendered_as_links(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        foreach (['javascript:alert(1)', ' JaVaScRiPt:alert(1)', "java\tscript:alert(1)", 'data:text/html,<script>alert(1)</script>', 'vbscript:msgbox(1)'] as $url) {
            $item = new MenuItem(['type' => MenuItemType::Link, 'url' => $url, 'menu_id' => $menu->id]);

            $this->assertFalse(MenuItem::isSafeUrl($url), $url);
            $this->assertSame('#', $item->link, $url);
            $this->assertFalse($item->is_link_resolved, $url);
            $this->assertSame('Invalid URL: unsupported scheme', $item->link_error, $url);
        }

        foreach (['https://example.com', '/about', 'mailto:me@example.com', 'tel:+123', '#'] as $url) {
            $this->assertTrue(MenuItem::isSafeUrl($url), $url);
        }
    }

    /**
     * The components are driven directly rather than through `Livewire::test()`
     * so the tests also run against the lowest supported Livewire release,
     * whose test harness cannot render components under Testbench.
     */
    private function menuBuilder(Menu $menu): MenuBuilder
    {
        $component = new MenuBuilder;
        $component->mount($menu->id);

        return $component;
    }

    private function menuItemForm(Menu $menu): MenuItemForm
    {
        $component = new MenuItemForm;
        $component->mount($menu->id);

        return $component;
    }

    public function test_save_rejects_items_from_another_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $own = $this->makeItem($menu);
        $foreign = $this->makeItem($other, 'Foreign');

        $component = $this->menuBuilder($menu);
        $component->data = [['id' => $own->id, 'children' => [['id' => $foreign->id, 'children' => []]]]];

        $this->expectException(\InvalidArgumentException::class);

        $component->save();
    }

    public function test_save_rejects_items_without_id(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        $component = $this->menuBuilder($menu);
        $component->data = [['name' => 'Injected', 'type' => 'link', 'children' => []]];

        $this->expectException(\InvalidArgumentException::class);

        $component->save();
    }

    public function test_save_ignores_extra_attributes(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $parent = $this->makeItem($menu, 'Parent');
        $child = $this->makeItem($menu, 'Child');

        $component = $this->menuBuilder($menu);
        $component->data = [[
            'id' => $parent->id,
            'name' => 'Hacked',
            'menu_id' => $other->id,
            'url' => 'javascript:alert(1)',
            'children' => [['id' => $child->id, 'children' => []]],
        ]];
        $component->save();

        $parent->refresh();
        $child->refresh();

        $this->assertSame('Parent', $parent->name);
        $this->assertSame($menu->id, $parent->menu_id);
        $this->assertSame('/page', $parent->url);
        $this->assertSame($parent->id, $child->parent_id);
    }

    public function test_menu_id_is_locked(): void
    {
        foreach ([MenuBuilder::class, MenuItemForm::class] as $component) {
            $attributes = (new \ReflectionProperty($component, 'menuId'))->getAttributes(Locked::class);

            $this->assertCount(1, $attributes, $component);
        }
    }

    public function test_actions_cannot_target_items_of_another_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $foreign = $this->makeItem($other, 'Foreign');

        $component = $this->menuBuilder($menu);

        foreach (['deleteAction', 'editAction', 'duplicateAction', 'createSubItemAction'] as $method) {
            $action = $component->{$method}()
                ->livewire($component)
                ->arguments(['menuItemId' => $foreign->id]);

            try {
                $action->call(['data' => ['name' => 'Hacked']]);
                $this->fail("{$method} accepted an item from another menu");
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('not found', $e->getMessage(), $method);
            }
        }

        $foreign->refresh();

        $this->assertSame('Foreign', $foreign->name);
        $this->assertSame(1, MenuItem::query()->count());
    }

    public function test_menu_item_form_ignores_injected_attributes(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        $component = $this->menuItemForm($menu);
        $component->data = array_merge($component->data, [
            'name' => 'New',
            'type' => MenuItemType::Link,
            'url' => '/new',
            'target' => '_self',
            'id' => 999,
        ]);
        $component->submit();

        $item = MenuItem::query()->where('name', 'New')->sole();

        $this->assertNotSame(999, $item->id);
        $this->assertSame($menu->id, $item->menu_id);
    }

    public function test_menu_item_form_rejects_script_urls(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        $component = $this->menuItemForm($menu);
        $component->data = array_merge($component->data, [
            'name' => 'Evil',
            'type' => MenuItemType::Link,
            'url' => 'javascript:alert(document.cookie)',
            'target' => '_self',
        ]);

        try {
            $component->submit();
            $this->fail('A javascript: URL was accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('data.url', $e->errors());
        }

        $this->assertSame(0, MenuItem::query()->count());
    }

    public function test_menuable_class_must_be_configured(): void
    {
        config()->set('filament-menu-builder.models', ['Test' => TestModel::class]);

        $this->assertSame(TestModel::class, MenuItemResource::resolveMenuableClass(TestModel::class));

        $this->expectException(\InvalidArgumentException::class);

        MenuItemResource::resolveMenuableClass(Menu::class);
    }

    public function test_translated_search_binds_locale(): void
    {
        if (! trait_exists('Spatie\\Translatable\\HasTranslations')) {
            eval('namespace Spatie\\Translatable; trait HasTranslations {}');
        }

        $model = new class extends TestModelWithTranslations
        {
            use \Spatie\Translatable\HasTranslations;
        };

        $query = $model->newQuery()->filamentSearch('foo', "en\"'))) OR 1=1 -- ");

        $this->assertStringNotContainsString('OR 1=1', $query->toSql());
        $this->assertContains('$."en\\"\'))) OR 1=1 -- "', $query->getBindings());
    }
}
