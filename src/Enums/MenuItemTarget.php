<?php

namespace Biostate\FilamentMenuBuilder\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuItemTarget: string implements HasLabel
{
    case Self = '_self';
    case Blank = '_blank';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Self => __('filament-menu-builder::menu-builder.target._self'),
            self::Blank => __('filament-menu-builder::menu-builder.target._blank'),
        };
    }
}
