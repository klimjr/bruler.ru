<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecommendationProducts extends Component
{
    public $products;
    private const CATEGORY_MAPPING = [
        't-shirts' => 'pants',
        'hoodies' => 'pants',
        'zip-hoodies' => 'pants',
        'half-zips' => 'pants',
        'pants' => 't-shirts',
        'gift-cards' => '',
        'accessories' => 'accessories',
        'jeans' => 'shirts',
        'shirts' => 'jeans',
        'sweaters' => 'pants'
    ];

    public function __construct(public $productId)
    {
        $p = Product::where('id', $productId)->with('category')->first();
        $slug = $p->category->slug;
        $this->getRelatedProducts($slug, $p);
    }

    public function getRelatedProducts($slug, $p)
    {
        $relatedCategory = self::CATEGORY_MAPPING[$slug] ?? 'pants';
        $this->products = $relatedCategory ? $this->getRandomProducts($p, $relatedCategory) : [];
    }

    private function getRandomProducts($product, $slug)
    {
        return Product::inRandomOrder()
            ->where('id', '!=', $product->id)
            ->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', '=', $slug);
            })->take(3)->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.recommendation-products');
    }
}
