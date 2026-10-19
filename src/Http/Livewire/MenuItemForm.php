<?php

namespace Biostate\FilamentMenuBuilder\Http\Livewire;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @property int $menuId
 * @property array|null $data
 * @property Schema $form
 */
class MenuItemForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    #[Locked]
    public int $menuId;

    public ?array $data = [];

    public function mount(int $menuId): void
    {
        $this->menuId = $menuId;

        $this->authorizeAccess();

        $this->form->fill();
    }

    public function hydrate(): void
    {
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        $plugin = FilamentMenuBuilderPlugin::get();
        $menu = $plugin->getMenuModel()::query()->findOrFail($this->menuId);

        abort_unless($plugin->getMenuResource()::canEdit($menu), 403);
    }

    public function form(Schema $schema): Schema
    {
        $panel = Filament::getCurrentPanel();
        if (! $panel) {
            throw new \RuntimeException('No active Filament panel');
        }

        /** @var FilamentMenuBuilderPlugin|null $plugin */
        $plugin = $panel->getPlugin('filament-menu-builder');
        if (! $plugin instanceof FilamentMenuBuilderPlugin) {
            throw new \RuntimeException('Filament Menu Builder plugin not registered');
        }

        /** @var class-string<MenuItemResource> $menuItemResource */
        $menuItemResource = $plugin->getMenuItemResource();

        return $schema
            ->components([
                Section::make(__('filament-menu-builder::menu-builder.menu_item'))
                    ->description(__('filament-menu-builder::menu-builder.create_new_menu_item'))
                    ->schema($menuItemResource::getFormSchemaArray())
                    ->footerActions([
                        Action::make('submit')
                            ->label(__('filament-menu-builder::menu-builder.create_menu_item'))
                            ->submit('submit'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        // Use the validated form state rather than the raw `$data` property,
        // which the client can fill with arbitrary attributes.
        $menuItem = array_merge($this->form->getState(), [
            'menu_id' => $this->menuId,
        ]);

        $menuItem = MenuItem::query()->create($menuItem);

        $this->form->fill();

        $this->dispatch('menu-item-created', menuId: $this->menuId, menuItemId: $menuItem->id);
    }

    public function render()
    {
        return view('filament-menu-builder::livewire.menu-item-form');
    }
}
