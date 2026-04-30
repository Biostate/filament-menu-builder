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
