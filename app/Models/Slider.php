<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
  protected $table = 'slider';
  protected $fillable = [
    'image',
    'name',
    'position',
  ];

  public function getImageUrlAttribute()
  {
    return $this->image ? asset('storage/' . $this->image) : null;
  }
}
