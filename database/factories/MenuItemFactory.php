<?php

namespace Biostate\FilamentMenuBuilder\Database\Factories;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Biostate\FilamentMenuBuilder\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'target' => '_self',
            'type' => MenuItemType::Link,
            'url' => $this->faker->url(),
            'route' => null,
            'route_parameters' => null,
            'link_class' => null,
            'wrapper_class' => null,
            'parameters' => null,
            'menuable_id' => null,
            'menuable_type' => null,
            'use_menuable_name' => false,
            'menu_id' => \Biostate\FilamentMenuBuilder\Models\Menu::factory(),
        ];
    }

    /**
     * Indicate that the menu item is a route type.
     */
    public function route(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MenuItemType::Route,
            'route' => 'home',
            'route_parameters' => ['id' => 1],
            'url' => null,
        ]);
    }

    /**
     * Indicate that the menu item is a model type.
     */
    public function model(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MenuItemType::Model,
            'menuable_type' => 'Biostate\\FilamentMenuBuilder\\Tests\\Models\\TestModel',
            'menuable_id' => 1,
            'use_menuable_name' => true,
            'url' => null,
            'route' => null,
        ]);
    }
}
