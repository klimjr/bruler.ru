<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyMade extends Model
{
    use HasFactory;
    protected $fillable = [
        'preview',
        'products',
    ];
    protected $casts = [
        'products' => 'array'
    ];
}
