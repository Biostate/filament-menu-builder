# Menuable Trait

You can create relationships between menu items and your models. To enable this feature, you need to add the `Menuable` trait to your model and implement the required methods.

## Basic Usage

Add the `Menuable` trait to your model and implement the `getMenuLinkAttribute` method:

```php
use Biostate\FilamentMenuBuilder\Traits\Menuable;

class Product extends Model
{
    use Menuable;
    
    public function getMenuLinkAttribute(): string
    {
        return route('products.show', $this);
    }
}
```

## Using Model Name as Menu Item Name

If you want to use the model name as the menu item name, you can use the `getMenuNameAttribute` method:

```php
use Biostate\FilamentMenuBuilder\Traits\Menuable;

class Product extends Model
{
    use Menuable;
    
    public function getMenuLinkAttribute(): string
    {
        return route('products.show', $this);
    }
    
    public function getMenuNameAttribute(): string
    {
        return $this->name;
    }
}
```

## Customizing the Search Column

When users search for a model in the menu item form, the trait builds a `LIKE` query against a real database column. By default that column is `name`. If your model uses a different column (for example `title`), override `getFilamentSearchLabel()`:

```php
use Biostate\FilamentMenuBuilder\Traits\Menuable;

class Page extends Model
{
    use Menuable;

    public function getMenuLinkAttribute(): string
    {
        return route('pages.show', $this);
    }

    public function getMenuNameAttribute(): string
    {
        return $this->title;
    }

    public static function getFilamentSearchLabel(): string
    {
        return 'title';
    }
}
```

`getFilamentSearchLabel()` must return the name of an actual database column — it is used in the `WHERE` clause of the search query. The display label shown in the select field comes from `getMenuNameAttribute()`, so the two methods are independent: use `getMenuNameAttribute()` to control how the option is rendered, and `getFilamentSearchLabel()` to control which column the search runs against.

## Registering Models in Config

After adding the trait to your model, you need to register it in the config file. You can add multiple models:

```php
return [
    'models' => [
        'Product' => \App\Models\Product::class,
        'Category' => \App\Models\Category::class,
    ],
];
```

## Using in Menu Item Forms

Once you've added these configurations, you can see the menu items in the menu item forms as a select input. This allows you to easily link menu items to your models directly from the Filament admin panel.

## Upgrading

### From <5.0.2

Before 5.0.2, the menu item Select displayed labels by reading the column named by `getFilamentSearchLabel()` directly from the database. Starting in 5.0.2 it routes labels through the `menu_name` accessor (`getMenuNameAttribute()`) instead. The two methods are now independent:

- `getFilamentSearchLabel()` controls the column used in the search `WHERE` clause.
- `getMenuNameAttribute()` controls the visible label.

If you previously overrode `getFilamentSearchLabel()` to point at a non-`name` column (for example `'title'`) and *didn't* also override `getMenuNameAttribute()`, the Select will now render empty labels instead of the column value. Add a `getMenuNameAttribute()` that returns the same column to restore the old behavior:

```php
public function getMenuNameAttribute(): string
{
    return (string) ($this->title ?? '');
}
```

### From <5.0.4

The default `getMenuNameAttribute()` provided by the trait used to return `$this->name` directly. If `$this->name` was null (or the column didn't exist), this raised a `TypeError` at access time because the method declares `: string`. Starting in 5.0.4 the default coalesces to an empty string:

```php
public function getMenuNameAttribute(): string
{
    return (string) ($this->name ?? '');
}
```

If you've overridden `getMenuNameAttribute()` in your own models, this change has no effect on you. If you were relying on the default for models without a `name` column, your code now returns `''` instead of throwing — usually a strict improvement, but worth noting if any of your tests assert on the exception.
