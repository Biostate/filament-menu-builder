<?php

namespace Biostate\FilamentMenuBuilder\DTO;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Illuminate\Support\Collection;

class MenuItem
{
    public function __construct(
        public string $name,
        public string $target,
        public ?string $wrapper_class,
        public ?string $link_class,
        public string $link,
        public MenuItemType $type,
        public ?string $route,
        public ?Collection $route_parameters,
        public int $menu_id,
        public ?string $menuable_id,
        public ?string $menuable_type,
        public ?string $url,
        public bool $use_menuable_name,
        public ?Collection $parameters,
        public ?Collection $children,
    ) {
        //
    }

    public static function fromModel($model): self
    {
        return new MenuItem(
            name: $model->menu_name,
            target: $model->target,
            wrapper_class: $model->wrapper_class,
            link_class: $model->link_class,
            link: $model->link,
            type: $model->type,
            route: $model->route,
            route_parameters: $model->route_parameters,
            menu_id: $model->menu_id,
            menuable_id: $model->menuable_id,
            menuable_type: $model->menuable_type,
            url: $model->url,
            use_menuable_name: $model->use_menuable_name,
            parameters: $model->parameters,
            children: $model->children->map(fn ($child) => static::fromModel($child)),
        );
    }

    public static function fromCollection(Collection $collection): Collection
    {
        return $collection->map(fn ($item) => static::fromModel($item));
    }
}
