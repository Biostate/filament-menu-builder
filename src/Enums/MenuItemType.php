<?php

namespace Biostate\FilamentMenuBuilder\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuItemType: string implements HasLabel
{
    case Link = 'link';
    case Route = 'route';
    case Model = 'model';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Link => __('filament-menu-builder::menu-builder.type.link'),
            self::Route => __('filament-menu-builder::menu-builder.type.route'),
            self::Model => __('filament-menu-builder::menu-builder.type.model')
        };
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'route' => self::Route,
            'model' => self::Model,
            default => self::Link,
        };
    }
}
