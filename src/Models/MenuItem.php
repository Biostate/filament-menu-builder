<?php

namespace Biostate\FilamentMenuBuilder\Models;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property MenuItemType $type
 * @property string $name
 * @property string|null $link_class
 * @property string|null $wrapper_class
 * @property string $target
 * @property string|null $route
 * @property string|null $url
 * @property string|null $menuable_type
 * @property string|int|null $menuable_id
 * @property Collection $parameters
 * @property Collection $route_parameters
 * @property bool $use_menuable_name
 * @property-read string $menu_name
 * @property-read string $normalized_type
 * @property-read string $link
 * @property-read \Illuminate\Database\Eloquent\Model|null $menuable
 * 
 * @method static void rebuildTree(array $data, bool $delete = false, mixed $root = null)
 */
class MenuItem extends Model
{
    use HasFactory;
    use NodeTrait;

    protected $fillable = [
        'id',
        'name',
        'target',
        'type',
        'route',
        'route_parameters',
        'link_class',
        'wrapper_class',
        'menu_id',
        'parameters',
        'menuable_id',
        'menuable_type',
        'url',
        'use_menuable_name',
    ];

    protected $casts = [
        'parameters' => 'collection',
        'route_parameters' => 'collection',
        'type' => MenuItemType::class,
    ];

    public $timestamps = false;

    protected $touches = ['menu'];

    public function menuable(): MorphTo
    {
        return $this->morphTo();
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function getMenuNameAttribute($value): string
    {
        $name = $this->attributes['name'];
        if ($this->type === MenuItemType::Model && $this->use_menuable_name) {
            $name = $this->menuable?->menu_name ?? $this->attributes['name'];
        }

        return $name ?? $this->attributes['name'];
    }

    public function getNormalizedTypeAttribute($value): string
    {
        if ($this->type !== MenuItemType::Model) {
            return $this->type->getLabel();
        }

        return Str::afterLast($this->menuable_type, '\\');
    }

    public function getLinkAttribute($value): string
    {
        return match ($this->type) {
            MenuItemType::Model => $this->menuable?->menu_link ?? '#',
            MenuItemType::Link => $this->resolveUrl(),
            default => route($this->route, $this->route_parameters->toArray()),
        };
    }

    public function resolveUrl(): string
    {
        if (! $this->url) {
            return url('/');
        }

        if ($this->url === '#') {
            return '#';
        }

        return Str::startsWith($this->url, 'http') ? $this->url : url($this->url);
    }
}
