<?php

namespace Biostate\FilamentMenuBuilder\Tests\Models;

use Biostate\FilamentMenuBuilder\Traits\Menuable;
use Illuminate\Database\Eloquent\Model;

class TestModelWithTranslations extends Model
{
    use Menuable;

    protected $fillable = ['name'];

    protected $table = 'test_models_with_translations';

    // Simulate Spatie Translatable behavior
    public function getTranslations($key = null, $locale = null)
    {
        return [
            'en' => ['name' => $this->name . ' (EN)'],
            'tr' => ['name' => $this->name . ' (TR)'],
        ];
    }

    public function getMenuLinkAttribute(): string
    {
        return route('test.show', ['model' => $this->id ?? 1]);
    }

    public function getMenuNameAttribute(): string
    {
        return $this->name ?? '';
    }
}
