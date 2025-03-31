<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $fillable = [
    'name',
    'image',
    'slug',
    'seo',
    'seo_title',
    'discount'
  ];

  protected $casts = [
    'seo' => 'array'
  ];

  public function getImageUrlAttribute()
  {
    return $this->image ? asset('storage/' . $this->image) : null;
  }

  public function getListUrl()
  {
    return route('products', ['category' => $this->slug]);
  }

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
