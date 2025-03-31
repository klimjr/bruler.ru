<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductHistory extends Component
{
    public $products;
    public $created;
    public $order;
    public $insert_products = [];

    public function mount()
    {
        foreach ($this->products as $product) {
            if (array_key_exists('variant', $product)) {
                $db_product = Product::where('id', $product['id'])->first();
                $db_product_variant = ProductVariant::where('id', $product['variant'])->first();

                $this->insert_products[] = [
                    'order' => $this->order,
                    'created' => $this->formatDate($this->created),
                    'price' => $product['price'],
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'image' => $db_product->image ?? '',
                    'size' => ($db_product_variant) ? Size::where('id', $db_product_variant['size_id'])->first()['name'] : ''
                ];
            }
        }
    }

    private function formatDate($date)
    {
        return $date ? date('d.m.Y', strtotime($date)) : '';
    }

    public function render()
    {
        return view('livewire.product-history');
    }
}
