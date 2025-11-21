<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources;

use BackedEnum;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    public static function getModel(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuModel();
    }

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bars-3';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-menu-builder::menu-builder.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menus');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                    ->required()
                    ->autofocus()
                    ->placeholder(__('filament-menu-builder::menu-builder.form_placeholders.name'))
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.slug'))
                    ->required()
                    ->unique(table: 'menus', column: 'slug', ignoreRecord: true)
                    ->placeholder(__('filament-menu-builder::menu-builder.form_placeholders.slug'))
                    ->hidden(fn ($context) => $context === 'create')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Component')
                    ->copyable()
                    ->copyMessage(__('filament-menu-builder::menu-builder.component_copy_message'))
                    ->copyMessageDuration(3000)
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (string $state): string => "<x-filament-menu-builder::menu slug=\"{$state}\" />"),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Actions\Action::make(__('filament-menu-builder::menu-builder.configure_menu'))
                    ->url(fn (Menu $record): string => static::getUrl('build', ['record' => $record]))
                    ->icon('heroicon-o-bars-3'),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {

        return [
            'index' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages\ListMenus::route('/'),
            'create' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages\CreateMenu::route('/create'),
            'edit' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages\EditMenu::route('/{record}/edit'),
            'build' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource\Pages\MenuBuilder::route('/{record}/build'),
        ];
    }
}
