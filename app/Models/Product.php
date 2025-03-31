<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const TYPE_PRODUCT = 'product';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_SET = 'set';

    protected $fillable = [
        'name',
        'name_en',
        'price',
        'image',
        'slug',
        'description',
        'category_id',
        'structure',
        'gallery',
        'type',
        'certificate_params',
        'product_care',
        'size_chart',
        'position',
        'classifier',
        'seo',
        'seo_title',
        'discount',
        'preorder',
        'set_products',
        'collection',
        'final_price',
        'technology_id',
        'new',
        'release_date',
        'mockup',
        'back_img',
        'show',
        'sort'
    ];

    protected $casts = [
        'gallery' => 'array',
        'certificate_params' => 'array',
        'size_chart' => 'array',
        'seo' => 'array',
        'set_products' => 'array'
    ];

    protected static function booted()
    {
        static::saved(function ($promotion) {
            Cache::forget('actions');
            app('actions');
        });

        static::deleted(function ($promotion) {
            Cache::forget('actions');
            app('actions');
        });

        static::saving(function ($product) {
            if ($product->type == self::TYPE_SET && !is_null($product->set_products)) {
                $setPrice = 0;
                foreach ($product->set_products as $setProduct) {
                    $queryProduct = Product::where('id', $setProduct['product_id'][0])->first();
                    $setPrice += ($queryProduct->discount) ? $queryProduct->price * ($queryProduct->discount / 100) : $queryProduct->price;
                }
                $product->price = $setPrice;
            }

            if (!is_null($product->final_price) && $product->price > 0) {
                $product->discount = (($product->price - $product->final_price) / $product->price) * 100;
            }
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteUrl()
    {
        return route('product', ['category' => $this->category->slug, 'product' => $this->slug]);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'product_id');
    }

    public function favouritesBy()
    {
        return $this->belongsToMany(User::class, 'favourites');
    }

    public function availableColors()
    {
        return $this->variants()->with('color')->get()->pluck('color')->unique('id');
    }

    public function availableSizes()
    {
        return $this->variants()->with('size')->get()->pluck('size')->unique('id');
    }

    public function getDiscountedPrice()
    {
        return $this->price - ($this->price * $this->discount / 100);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function getActions()
    {
        $actions = app('actions');

        foreach ($actions as $action) {
            if ($action['all_products'] && !in_array($this->id, $action['products_exclude'])) {
                return $actions;
            }

            if (in_array($this->id, $actions['products_include'])) {
                return $actions;
            }
        }

        return null;
    }

    public function getActionDiscountedPrice()
    {
        if (!$this->getActions()) {
            return $this->price;
        }

        $action = $this->getActions()->first();
        if (!$action) {
            return $this->price;
        }
        if ($action['discount_type'] === 'percentage') {
            return $this->price - ($this->price * $action['discount_amount'] / 100);
        }

        return $this->price - $action['discount_amount'];
    }
}
