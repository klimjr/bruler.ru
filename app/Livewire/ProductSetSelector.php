<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Livewire\Component;

class ProductSetSelector extends Component
{
    public Product $product;

    public $productsInSet = [];
    public $selectedProduct = [];

    public function boot() {
        if (!$this->productsInSet) {
            foreach ($this->product->set_products as $ind => $product_set) {
                $productsCounter = 0;
                foreach ($product_set['product_id'] as $product_set_id) {
                    $setProduct = Product::where('id', $product_set_id)->first();
                    $setVariants = ProductVariant::where('product_id', $product_set_id)->get();

                    if ($productsCounter == 0 && count($product_set['product_id']) > 1) {
                        $this->productsInSet[$ind]['selected'] = $ind;

                        if (count($setVariants) > 1) {
                            $this->productsInSet[$ind][] = [
                                'id' => $setProduct['id'],
                                'name_en' => $setProduct['name_en'],
                                'image' => $setProduct['image'],
                                'variants' => $setVariants
                            ];
                        } else {
                            $this->productsInSet[$ind][] = [
                                'id' => $setProduct['id'],
                                'name_en' => $setProduct['name_en'],
                                'image' => $setProduct['image'],
                                'selectedVariant' => (String)$setVariants[0]->id
                            ];
                        }
    
                        $productsCounter++;
                    } else {
                        if (count($setVariants) > 1) {
                            $this->productsInSet[$ind][] = [
                                'id' => $setProduct['id'],
                                'name_en' => $setProduct['name_en'],
                                'image' => $setProduct['image'],
                                'variants' => $setVariants
                            ];
                        } else {
                            $this->productsInSet[$ind][] = [
                                'id' => $setProduct['id'],
                                'name_en' => $setProduct['name_en'],
                                'image' => $setProduct['image'],
                                'selectedVariant' => (String)$setVariants[0]->id
                            ];
                        }
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.product-set-selector');
    }

    public function selectProduct($indexInSet, $productIndex)
    {
        $this->productsInSet[$indexInSet]['selected'] = $productIndex;
        unset($this->productsInSet[$indexInSet][$productIndex]['selectedVariant']);
        $this->dispatch('productSetUpdated', $this->productsInSet);
    }

    public function selectVariant(string $variant, $index, string $selectedProduct, string $amount) {
        if($amount < 1) return;
        $this->productsInSet[$index][$selectedProduct]['selectedVariant'] = $variant;
        $this->dispatch('productSetUpdated', $this->productsInSet);
    }
}
