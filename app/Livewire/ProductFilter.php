<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductFilter extends Component
{
    public $minPriceProduct;
    public $maxPriceProduct;
    public $totalFilteredProducts;

    public function __construct()
    {
      $this->minPriceProduct = Product::min('price');
      $this->maxPriceProduct = Product::max('price');
      $this->totalFilteredProducts = Product::count();
    }

    public function render()
    {
        return view('livewire.product-filter');
    }
}
