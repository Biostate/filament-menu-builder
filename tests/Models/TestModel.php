<?php

namespace Biostate\FilamentMenuBuilder\Tests\Models;

use Biostate\FilamentMenuBuilder\Traits\Menuable;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use Menuable;
    
    protected $fillable = ['name'];
    
    protected $table = 'test_models';
    
    public function getMenuLinkAttribute(): string
    {
        return route('test.show', ['model' => $this->id ?? 1]);
    }
    
    public function getMenuNameAttribute(): string
    {
        return $this->name ?? '';
    }
}
