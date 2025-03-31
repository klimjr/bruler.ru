<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdekPvz extends Model
{
    protected $fillable = [
        'code',
        'city_code',
        'address',
        'phones',
        'work_time',
        'is_dressing_room',
        'address_comment',
        'location_latitude',
        'location_longitude'
    ];
}
