<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $casts = [
        'products' => 'array'
    ];
    protected $guarded = [];
}
