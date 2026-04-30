<?php

namespace Biostate\FilamentMenuBuilder\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait Menuable
{
    public function getMenuLinkAttribute(): string
    {
        throw new \Exception('You need to implement the menuLink method');
    }

    public function getMenuNameAttribute(): string
    {
        return $this->name;
    }

    public static function getFilamentSearchLabel(): string
    {
        return 'name';
    }

    public function scopeFilamentSearch(Builder $query, $search, $locale = null)
    {
        $hasTranslations = in_array('Spatie\Translatable\HasTranslations', class_uses_recursive(static::class));

        if ($hasTranslations) {
            $locale = $locale ?? app()->getLocale();
            $query->whereRaw(
                'LOWER(json_unquote(JSON_EXTRACT(`' . $this->getFilamentSearchLabel() . "`, '$.\"$locale\"'))) like LOWER(?)",
                ["%{$search}%"]
            );
        } else {
            $query->where($this->getFilamentSearchLabel(), 'like', "%{$search}%");
        }

        $query->limit(10);
    }

    public function getFilamentSearchOptionName()
    {
        return $this->menu_name;
    }
}
