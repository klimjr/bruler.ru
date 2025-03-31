<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'amount',
        'article',
        'length',
        'width',
        'height',
        'weight',
        'image'
    ];


    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function color() {
        return $this->belongsTo(Color::class);
    }
    public function size() {
        return $this->belongsTo(Size::class);
    }

    public function getImageUrlAttribute() {
        return asset('storage/' . $this->image);
    }
}
