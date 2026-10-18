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
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

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

    public function test_save_rejects_items_from_another_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $own = $this->makeItem($menu);
        $foreign = $this->makeItem($other, 'Foreign');

        $this->expectException(\InvalidArgumentException::class);

        Livewire::test(MenuBuilder::class, ['menuId' => $menu->id])
            ->set('data', [['id' => $own->id, 'children' => [['id' => $foreign->id, 'children' => []]]]])
            ->call('save');
    }

    public function test_save_rejects_items_without_id(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        $this->expectException(\InvalidArgumentException::class);

        Livewire::test(MenuBuilder::class, ['menuId' => $menu->id])
            ->set('data', [['name' => 'Injected', 'type' => 'link', 'children' => []]])
            ->call('save');
    }

    public function test_save_ignores_extra_attributes(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $parent = $this->makeItem($menu, 'Parent');
        $child = $this->makeItem($menu, 'Child');

        Livewire::test(MenuBuilder::class, ['menuId' => $menu->id])
            ->set('data', [[
                'id' => $parent->id,
                'name' => 'Hacked',
                'menu_id' => $other->id,
                'url' => 'javascript:alert(1)',
                'children' => [['id' => $child->id, 'children' => []]],
            ]])
            ->call('save');

        $parent->refresh();
        $child->refresh();

        $this->assertSame('Parent', $parent->name);
        $this->assertSame($menu->id, $parent->menu_id);
        $this->assertSame('/page', $parent->url);
        $this->assertSame($parent->id, $child->parent_id);
    }

    public function test_menu_id_is_locked(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(MenuBuilder::class, ['menuId' => $menu->id])
            ->set('menuId', $other->id);
    }

    public function test_actions_cannot_target_items_of_another_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $foreign = $this->makeItem($other, 'Foreign');

        try {
            Livewire::test(MenuBuilder::class, ['menuId' => $menu->id])
                ->callAction('delete', arguments: ['menuItemId' => $foreign->id]);
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('not found', $e->getMessage());
        }

        $this->assertNotNull(MenuItem::find($foreign->id));
    }

    public function test_menu_item_form_ignores_injected_attributes(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);
        $other = Menu::factory()->create(['name' => 'Other']);
        $existing = $this->makeItem($other, 'Existing');

        Livewire::test(MenuItemForm::class, ['menuId' => $menu->id])
            ->set('data.name', 'New')
            ->set('data.type', 'link')
            ->set('data.url', '/new')
            ->set('data.target', '_self')
            ->set('data.id', $existing->id + 100)
            ->set('data.wrapper_class', 'ok')
            ->call('submit');

        $item = MenuItem::query()->where('name', 'New')->sole();

        $this->assertNotSame($existing->id + 100, $item->id);
        $this->assertSame($menu->id, $item->menu_id);
    }

    public function test_menu_item_form_rejects_script_urls(): void
    {
        $menu = Menu::factory()->create(['name' => 'Main']);

        Livewire::test(MenuItemForm::class, ['menuId' => $menu->id])
            ->set('data.name', 'Evil')
            ->set('data.type', 'link')
            ->set('data.url', 'javascript:alert(document.cookie)')
            ->set('data.target', '_self')
            ->call('submit')
            ->assertHasErrors(['data.url']);

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
