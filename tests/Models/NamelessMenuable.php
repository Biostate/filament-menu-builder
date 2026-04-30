<?php

namespace Biostate\FilamentMenuBuilder\Tests\Models;

use Biostate\FilamentMenuBuilder\Traits\Menuable;
use Illuminate\Database\Eloquent\Model;

class NamelessMenuable extends Model
{
    use Menuable;

    protected $fillable = ['title'];

    protected $table = 'nameless_menuables';

    public function getMenuLinkAttribute(): string
    {
        return route('test.show', ['model' => $this->id ?? 1]);
    }

    public function getMenuNameAttribute(): string
    {
        return (string) ($this->title ?? '');
    }
}
