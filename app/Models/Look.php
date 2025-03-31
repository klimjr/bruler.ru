<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Look extends Model
{
    protected $guarded = [];
    protected $casts = [
        'products' => 'array'
    ];

    public function getProductCountAttribute(): int
    {
        return count($this->products);
    }

}
