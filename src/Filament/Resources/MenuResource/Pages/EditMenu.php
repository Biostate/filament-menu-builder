<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getActions(): array
    {
        $resource = static::getResource();
        return [
            Actions\Action::make(__('filament-menu-builder::menu-builder.configure_menu'))
                ->url(fn (Menu $record): string => $resource::getUrl('build', ['record' => $record]))
                ->icon('heroicon-o-bars-3'),
            Actions\Action::make(__('filament-menu-builder::menu-builder.regerate_slug'))
                ->action(function (Menu $record) {
                    $record->generateSlug();
                    $record->save();
                })
                ->after(fn () => $this->fillForm())
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-path'),
            Actions\DeleteAction::make(),
        ];
    }
}
