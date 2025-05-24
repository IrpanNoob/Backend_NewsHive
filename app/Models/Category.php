<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    protected $fillable = [
        'name',
        'slug',
    ];
}
