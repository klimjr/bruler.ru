<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RunningText extends Model
{
    protected $fillable = [
        'text',
        'text_color',
        'bg_color',
    ];
}
