<?php

namespace App\Livewire;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
class OrderItem extends Component
{
  public $product;
  public $quantity;
  public $variant;
  public $type;
  public $certificate;
  public $set_products;
  public $is_free;
  public function mount($product, $quantity, $type, $variant, $certificate, $set_products, $is_free)
  {
    $this->product = Product::find($product['id']);
    $this->quantity = $quantity;
    $this->type = $type;
    $this->is_free = $is_free;
    $this->variant = isset($variant) ? ProductVariant::find($variant) : null;
    $this->certificate = $certificate ?? null;
    $this->set_products = $set_products ?? null;
  }

  public function render()
  {
    return view("livewire.order-item");
  }
}
