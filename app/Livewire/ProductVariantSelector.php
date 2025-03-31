<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Livewire\Component;

class ProductVariantSelector extends Component
{
  public Product $product;

  public $selectedColor;

  public $colors = [];

  public $sizes = [];
  public $selectedVariant;

  public $variants = [];
  protected $listeners = ['productVariantUpdated' => 'variantSelected'];

  public function boot()
  {
    $this->colors = $this->product->availableColors()->each(function ($color) {
      $color['type'] = 'color';
      return $color;
    });

    if (isset($this->product->classifier)) {
      $other_products = Product::where('classifier', $this->product->classifier)
        ->where('id', '<>', $this->product->id)
        ->get();
      if ($other_products->isNotEmpty()) {
        $other_products->each(function ($product) {
          $product->availableColors()->each(function ($color) use ($product) {
            $color['type'] = 'link';
            $color['link'] = $product->getRouteUrl();
            $this->colors[] = $color;
          });
        });
      }
    }

    $this->sizes = Size::all();

    $this->selectedColor = $this->selectedColor ?? $this->colors->first()?->id;

    $this->prepareData();
  }

  public function render()
  {
    return view('livewire.product-variant-selector');
  }

  public function variantSelected($productVariant)
  {
    $this->selectedVariant = $productVariant;
  }
  public function selectColor(int $color)
  {
    $this->selectedColor = $color;
    $this->selectedVariant = null;
    $this->prepareData();

    $this->dispatch('productColorUpdated', color: $color);
  }

  public function selectVariant(string $color, string $variant)
  {
    $this->selectedColor = $color;
    $this->prepareData();
    $v = ProductVariant::find($variant);
    if ($v->amount < 1)
      return;
    $this->selectedVariant = $variant;

    $this->dispatch('productVariantUpdated', productVariant: $variant);
  }

  public function prepareData()
  {
    $this->variants = $this->product->variants()->where('color_id', $this->selectedColor)->get();
  }
}


