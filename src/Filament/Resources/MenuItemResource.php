<?php

namespace Biostate\FilamentMenuBuilder\Filament\Resources;

use BackedEnum;
use Biostate\FilamentMenuBuilder\Enums\MenuItemTarget;
use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Filament\Actions;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class MenuItemResource extends Resource
{
    public static function getModel(): string
    {
        return \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuItemModel();
    }

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bars-3';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-menu-builder::menu-builder.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menu_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menu_items');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema(static::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.url')),
                Tables\Columns\TextColumn::make('menu.name')
                    ->label(__('filament-menu-builder::menu-builder.menu_name')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
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

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                ->autofocus()
                ->required()
                ->maxLength(255),
            Select::make('menu_id')
                ->label(__('filament-menu-builder::menu-builder.menu_name'))
                ->options(fn () => \Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuModel()::all()->pluck('name', 'id'))
                ->hidden(fn ($context) => ! in_array($context, ['edit', 'create']))
                ->required(),
            Select::make('target')
                ->label(__('filament-menu-builder::menu-builder.form_labels.target'))
                ->options(MenuItemTarget::class)
                ->default('_self')
                ->required(),
            TextInput::make('link_class')
                ->label(__('filament-menu-builder::menu-builder.form_labels.link_class'))
                ->maxLength(255),
            TextInput::make('wrapper_class')
                ->label(__('filament-menu-builder::menu-builder.form_labels.wrapper_class'))
                ->maxLength(255),
            \Filament\Schemas\Components\Fieldset::make('URL')
                ->columns(1)
                ->schema([
                    Select::make('type')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.type'))
                        ->options(MenuItemType::class)
                        ->afterStateUpdated(function (callable $set) {
                            $set('menuable_type', null);
                            $set('menuable_id', null);
                            $set('url', null);
                        })
                        ->default('link')
                        ->required()
                        ->live(),
                    // URL
                    TextInput::make('url')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.url'))
                        ->hidden(fn ($get) => $get('type')->value != 'link')
                        ->maxLength(255)
                        ->required(fn ($get) => $get('type')->value == 'link'),
                    // ROUTE
                    Select::make('route')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.route'))
                        ->searchable()
                        ->helperText(__('filament-menu-builder::menu-builder.route_helper_text'))
                        ->options(
                            function () {
                                $excludedRoutes = config('filament-menu-builder.exclude_route_names', []);

                                $routes = collect(Route::getRoutes())
                                    ->filter(function ($route) use ($excludedRoutes) {
                                        $routeName = $route->getName();
                                        if (! $routeName) {
                                            return false;
                                        }

                                        // Check if the route name matches any of the excluded patterns
                                        $isExcluded = false;
                                        foreach ($excludedRoutes as $pattern) {
                                            if (preg_match($pattern, $routeName)) {
                                                $isExcluded = true;

                                                break;
                                            }
                                        }

                                        return ! $isExcluded;
                                    })
                                    ->mapWithKeys(function ($route) {
                                        return [$route->getName() => $route->getName()];
                                    });

                                return $routes;
                            }
                        )
                        ->hidden(fn ($get) => $get('type')->value != 'route')
                        ->required(fn ($get) => $get('type')->value == 'route')
                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                            if ($state === null) {
                                return [];
                            }
                            $route = app('router')->getRoutes()->getByName($state);
                            if (! $route) {
                                return [];
                            }

                            $uri = $route->uri();

                            preg_match_all('/\{(\w+?)\}/', $uri, $matches);
                            $parameters = $matches[1];

                            if (empty($parameters)) {
                                return [];
                            }

                            $set('route_parameters', array_fill_keys($parameters, null));
                        })
                        ->live(),
                    KeyValue::make('route_parameters')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.route_parameters'))
                        ->hidden(fn ($get) => $get('type')->value != 'route')
                        ->helperText(function ($get, $set, $operation) {
                            if ($get('route') === null) {
                                return __('filament-menu-builder::menu-builder.route_parameters_empty_helper_text');
                            }
                            $route = app('router')->getRoutes()->getByName($get('route'));
                            if (! $route) {
                                return __('filament-menu-builder::menu-builder.route_parameters_not_found_helper_text');
                            }

                            $uri = $route->uri();

                            preg_match_all('/\{(\w+?)\}/', $uri, $matches);
                            $parameters = $matches[1];

                            if (empty($parameters)) {
                                return __('filament-menu-builder::menu-builder.route_parameters_no_parameters_helper_text');
                            }

                            return __('filament-menu-builder::menu-builder.route_parameters_has_parameters_helper_text', [
                                'parameters' => implode(', ', $parameters),
                            ]);
                        }),
                    // MODEL
                    Select::make('menuable_type')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.menuable_type'))
                        ->options(
                            array_flip(config('filament-menu-builder.models', []))
                        )
                        ->live()
                        ->required(fn ($get) => $get('type')->value == 'model')
                        ->afterStateUpdated(fn (callable $set) => $set('menuable_id', null))
                        ->hidden(fn ($get) => empty(config('filament-menu-builder.models', [])) || $get('type')->value != 'model'),
                    Select::make('menuable_id')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.menuable_id'))
                        ->searchable()
                        ->options(fn ($get) => $get('menuable_type')::all()->pluck($get('menuable_type')::getFilamentSearchLabel(), 'id'))
                        ->getSearchResultsUsing(function (string $search, callable $get) {
                            $className = $get('menuable_type');

                            return $className::filamentSearch($search)->pluck($className::getFilamentSearchLabel(), 'id');
                        })
                        ->required(fn ($get) => $get('menuable_type') != null)
                        ->getOptionLabelUsing(fn ($value, $get): ?string => $get('menuable_type')::find($value)?->getFilamentSearchOptionName())
                        ->hidden(fn ($get) => $get('menuable_type') == null),
                    Toggle::make('use_menuable_name')
                        ->label(__('filament-menu-builder::menu-builder.form_labels.use_menuable_name'))
                        ->hidden(fn ($get) => $get('menuable_type') == null)
                        ->default(false),
                ]),
            KeyValue::make('parameters')
                ->label(__('filament-menu-builder::menu-builder.form_labels.parameters'))
                ->helperText(__('filament-menu-builder::menu-builder.parameters_helper_text', [
                    'parameters' => implode(', ', config('filament-menu-builder.usable_parameters', [])),
                ])),
        ];
    }

    public static function getFormSchemaArray(): array
    {
        return static::getFormSchema();
    }

    public static function getPages(): array
    {
        return [
            'index' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages\ListMenuItems::route('/'),
            'create' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages\CreateMenuItem::route('/create'),
            'edit' => \Biostate\FilamentMenuBuilder\Filament\Resources\MenuItemResource\Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
