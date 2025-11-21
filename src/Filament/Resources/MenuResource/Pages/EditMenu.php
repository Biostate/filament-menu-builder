<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages;

use Biostate\FilamentMenuBuilder\Models\Menu;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMenu extends EditRecord
{
    public static function getResource(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuResource();
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return [
            Action::make(__('filament-menu-builder::menu-builder.configure_menu'))
                ->url(fn (Menu $record): string => $resource::getUrl('build', ['record' => $record]))
                ->icon('heroicon-o-bars-3'),
            Action::make(__('filament-menu-builder::menu-builder.regerate_slug'))
                ->action(function (Menu $record) {
                    $record->generateSlug();
                    $record->save();
                })
                ->after(fn () => $this->fillForm())
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-path'),
            DeleteAction::make(),
        ];
    }
}
