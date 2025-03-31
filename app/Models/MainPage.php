<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner',
        'banner_mobile',
        'video',
        'video_mobile',
        'span_text',
        'main_text',
        'button_text',
        'button_link',
        'products_span_text',
        'products_main_text',
        'products',
        'one_plus_one',
        'timer'
    ];
    protected $casts = [
        'products' => 'array'
    ];
}

