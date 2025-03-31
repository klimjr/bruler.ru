<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'title',
        'desc',
        'is_new',
        'img1',
        'img2',
        'position'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'collection');
    }
}
