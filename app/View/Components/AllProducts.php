<?php

namespace App\View\Components;

use App\Models\Page;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AllProducts extends Component
{
    public $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->products = Product::all()
            ->sortBy(function ($product) {
                return $product->preorder ? 1 : 0;
            })
            ->sortBy(function ($product) {
                return $product->new ? 1 : 0;
            })
            ->sortBy(function ($product) {
                return !$product->preorder && !$product->new ? 1 : 0;
            })
            ->sortBy(function ($product) {
                return $product->discount > 0 ? 1 : 0;
            })
            ->sortBy(function ($product) {
                return $product->variants->every(function ($variant) {
                    return $variant->amount == 0;
                }) ? 1 : 0;
            });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $page = Page::where('type', Page::TYPE_MAIN_PAGE)->first();
        return view('components.all-products', compact('page'));
    }
}