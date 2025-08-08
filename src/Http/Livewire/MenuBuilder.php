<?php

namespace Biostate\FilamentMenuBuilder\Http\Livewire;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Size;
use Illuminate\Support\Collection;
use Livewire\Component;

class MenuBuilder extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public int $menuId;

    public array $data = [];

    protected $listeners = [
        'menu-item-created' => '$refresh',
    ];

    public function mount(int $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function deleteAction(): Action
    {
        // TODO: extend action and make new delete action for this component
        return Action::make('delete')
            ->tooltip(__('filament-menu-builder::menu-builder.delete_menu_item_tooltip'))
            ->size(Size::ExtraSmall)
            ->icon('heroicon-m-trash')
            ->iconButton()
            ->requiresConfirmation()
            ->modalHeading(__('filament-menu-builder::menu-builder.destroy_menu_item_heading'))
            ->modalDescription('Are you sure you want to delete this menu item? All items below will be deleted as well.')
            ->modalSubmitActionLabel(__('Destroy'))
            ->color('danger')
            ->action(function (array $arguments) {
                $menuItemId = $arguments['menuItemId'];

                $menuItem = MenuItem::find($menuItemId);
                if (! $menuItem) {
                    return;
                }
                MenuItem::descendantsOf($menuItem)->each(function (MenuItem $menuItem) {
                    $menuItem->delete();
                });

                $menuItem->delete();
            });
    }

    public function editAction(): Action
    {
        // TODO: extend action and make new edit action for this component
        return Action::make('edit')
            ->tooltip(__('filament-menu-builder::menu-builder.edit_menu_item_tooltip'))
            ->size(Size::ExtraSmall)
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->fillForm(function (array $arguments) {
                $menuItemId = $arguments['menuItemId'];
                $menuItem = MenuItem::find($menuItemId);

                return $menuItem->toArray();
            })
            ->form(fn () => [
                Grid::make()
                    ->schema(MenuItemResource::getFormSchema()),
            ])
            ->action(function (array $arguments, $data) {
                $menuItemId = $arguments['menuItemId'];

                $menuItem = MenuItem::find($menuItemId);
                if (! $menuItem) {
                    return;
                }

                $menuItem->update($data);
            });
    }

    public function createSubItemAction(): Action
    {
        // TODO: extend action and make new edit action for this component
        return Action::make('createSubItem')
            ->tooltip(__('filament-menu-builder::menu-builder.create_sub_item_tooltip'))
            ->size(Size::ExtraSmall)
            ->icon('heroicon-m-plus')
            ->iconButton()
            ->form(fn () => [
                Grid::make()
                    ->schema(MenuItemResource::getFormSchema()),
            ])
            ->action(function (array $arguments, $data) {
                $parent = MenuItem::find($arguments['menuItemId']);
                if (! $parent) {
                    return;
                }

                $menuItem = MenuItem::create([
                    ...$data,
                    'menu_id' => $this->menuId,
                ]);
                $parent->appendNode($menuItem);
            });
    }

    public function viewAction(): Action
    {
        // TODO: extend action and make new edit action for this component
        return Action::make('view')
            ->label(__('filament-menu-builder::menu-builder.view_menu_item_tooltip'))
            ->icon('heroicon-m-eye')
            ->url(fn (array $arguments) => MenuItemResource::getUrl('edit', ['record' => $arguments['menuItemId']]));
    }

    public function goToLinkAction(): Action
    {
        // TODO: extend action and make new edit action for this component
        return Action::make('goToLink')
            ->label(__('filament-menu-builder::menu-builder.go_to_link_tooltip'))
            ->icon('heroicon-m-link');
    }

    public function duplicateAction(): Action
    {
        // TODO: extend action and make new edit action for this component
        return Action::make('duplicate')
            ->tooltip(__('filament-menu-builder::menu-builder.duplicate_menu_item_tooltip'))
            ->size(Size::ExtraSmall)
            ->icon('heroicon-m-document-duplicate')
            ->iconButton()
            ->requiresConfirmation()
            ->modalDescription('Are you sure you want to duplicate this menu item?')
            ->action(function (array $arguments) {
                $menuItemId = $arguments['menuItemId'];
                $isEdit = isset($arguments['edit']);

                $menuItem = MenuItem::find($menuItemId);
                if (! $menuItem) {
                    return;
                }

                $newMenuItem = $menuItem->replicate();
                $newMenuItem->name = $newMenuItem->name . ' (copy)';
                $newMenuItem->afterNode($menuItem)->save();

                if ($isEdit) {
                    $this->replaceMountedAction('edit', [
                        'menuItemId' => $newMenuItem->id,
                    ]);
                }
            })
            ->extraModalFooterActions(fn (Action $action): array => [
                $action->makeModalSubmitAction('duplicateAndEdit', arguments: ['edit' => true])
                    ->label('Duplicate & Edit'),
            ]);
    }

    public function render()
    {
        return view('filament-menu-builder::livewire.menu-builder', [
            'items' => $this->items(),
        ]);
    }

    public function items(): Collection
    {
        return MenuItem::where('menu_id', $this->menuId)
            ->with('menuable')
            ->defaultOrder()
            ->get()
            ->toTree();
    }

    public function save(): void
    {
        if (empty($this->data)) {
            return;
        }

        MenuItem::rebuildTree($this->data);

        Notification::make()
            ->title(__('filament-menu-builder::menu-builder.menu_saved'))
            ->success()
            ->send();
    }
}
