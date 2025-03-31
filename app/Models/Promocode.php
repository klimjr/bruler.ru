<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'discount',
        'quantity',
        'active',
        'applies_to_all_products',
        'applicable_products'
    ];

    protected $casts = [
        'active' => 'boolean',
        'applies_to_all_products' => 'boolean',
        'applicable_products' => 'array'
    ];
}
