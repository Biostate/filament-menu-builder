<?php

namespace Biostate\FilamentMenuBuilder\Http\Livewire;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

/**
 * @property int $menuId
 * @property array|null $data
 * @property \Filament\Schemas\Schema $form
 */
class MenuItemForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public int $menuId;

    public ?array $data = [];

    public function mount(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu Item')
                    ->description('Create New Menu Item')
                    ->schema(MenuItemResource::getFormSchemaArray())
                    ->footerActions([
                        \Filament\Actions\Action::make('submit')
                            ->label(__('filament-menu-builder::menu-builder.create_menu_item'))
                            ->submit('submit'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $menuItem = array_merge($this->data, [
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
