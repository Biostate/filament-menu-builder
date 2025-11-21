<?php

namespace Biostate\FilamentMenuBuilder\Models;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Routing\Route;
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
 * @property-read bool $is_route_resolved
 * @property-read bool $is_url_resolved
 * @property-read bool $is_link_resolved
 * @property-read string|null $link_error
 * @property-read array $missing_route_parameters
 *
 * @method static void rebuildTree(array $data, bool $delete = false, mixed $root = null)
 * @method static \Illuminate\Database\Eloquent\Collection descendantsOf(mixed $id, array $columns = ['*'], bool $andSelf = false)
 * @method \Illuminate\Database\Eloquent\Collection toTree()
 * @method $this afterNode(self $node)
 * @method \Illuminate\Database\Eloquent\Builder defaultOrder(string $dir = 'asc')
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
        return $this->belongsTo(\Biostate\FilamentMenuBuilder\FilamentMenuBuilderPlugin::get()->getMenuModel());
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
            default => $this->resolveRoute(),
        };
    }

    public function getIsRouteResolvedAttribute(): bool
    {
        try {
            $this->resolveRoute();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getIsUrlResolvedAttribute(): bool
    {
        try {
            $this->resolveUrl();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getIsLinkResolvedAttribute(): bool
    {
        return match ($this->type) {
            MenuItemType::Model => $this->menuable && method_exists($this->menuable, 'getMenuLinkAttribute'),
            MenuItemType::Link => $this->is_url_resolved,
            default => $this->is_route_resolved,
        };
    }

    public function getLinkErrorAttribute(): ?string
    {
        return match ($this->type) {
            MenuItemType::Model => $this->getModelLinkError(),
            MenuItemType::Link => $this->getUrlError(),
            default => $this->getRouteError(),
        };
    }

    public function getMissingRouteParametersAttribute(): array
    {
        try {
            $this->resolveRoute();

            return [];
        } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $e) {
            return $this->extractMissingParameters();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getModelLinkError(): ?string
    {
        if (! $this->menuable) {
            return 'Model not found';
        }

        if (! method_exists($this->menuable, 'getMenuLinkAttribute')) {
            return 'Model does not implement menu_link attribute';
        }

        // Use reflection to safely access the menu_link attribute
        try {
            $reflection = new \ReflectionClass($this->menuable);
            if ($reflection->hasMethod('getMenuLinkAttribute')) {
                $method = $reflection->getMethod('getMenuLinkAttribute');
                $link = $method->invoke($this->menuable);

                return $link ? null : 'Model menu_link is empty';
            }
        } catch (\Exception $e) {
            return 'Model menu_link error: ' . $e->getMessage();
        }

        return 'Unable to access model menu_link';
    }

    private function getUrlError(): ?string
    {
        try {
            $this->resolveUrl();

            return null;
        } catch (\Exception $e) {
            return 'Invalid URL: ' . $e->getMessage();
        }
    }

    private function getRouteError(): ?string
    {
        try {
            $this->resolveRoute();

            return null;
        } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $e) {
            $missingParams = $this->extractMissingParameters();
            if (! empty($missingParams)) {
                return 'Missing route parameters: ' . implode(', ', $missingParams);
            }

            return 'Route not found: ' . $this->route;
        } catch (\Exception $e) {
            return 'Route error: ' . $e->getMessage();
        }
    }

    private function extractMissingParameters(): array
    {
        /* @var $route Route */
        $route = app('router')->getRoutes()->getByName($this->route);
        if (! $route) {
            return [];
        }

        $parameterNames = $route->parameterNames();
        $filteredRouteParameters = $this->route_parameters->filter(fn ($item) => $item['value'] !== null && $item['value'] !== '')->pluck('key')->toArray();

        return array_diff($parameterNames, $filteredRouteParameters);
    }

    public function resolveRoute(): string
    {
        return route($this->route, $this->route_parameters->pluck('value', 'key')->toArray());
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
