<?php

namespace App\Livewire;

use App\Models\Action;
use App\Models\StoreSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{

    public $selectAll = false;
    public $productCount = 0;

    public $allProducts = false;

    public function changeSelectAll()
    {
        $this->dispatch('cartSelectAllUpdated', selectAll: $this->selectAll);
    }

    #[On('cartUpdated')]
    public function onCartUpdate()
    {
        $this->updateSelectAll();
        $this->productCount = 0;

        $storeSetting = StoreSetting::first();
        $exception_products = [];

        if (count(session()->get('cart', [])) !== 0) {
            $cart = session()->get('cart', []);

            if (isset($storeSetting->events['exception_products']) && ($storeSetting->events['use_free_three_product']))
                $exception_products = $storeSetting->events['exception_products'];

            Log::info($exception_products);


            foreach ($cart as $index => $item) {
                if (!isset($item['id'])) {
                    unset($cart[$index]);
                    continue;
                }
                if ($item['selected']) {
                    if (count($exception_products) !== 0) {
                        if (!in_array($item['id'], $exception_products)) {
                            $this->productCount += $item['quantity'];
                        } else
                            $item['is_free'] = false;
                    } else
                        $this->productCount += $item['quantity'];
                }
            }
            session()->put("cart", $cart);
            $this->allProducts = true;
        } else
            $this->allProducts = false;

        $cart = session()->get("cart", []);

        if (isset($storeSetting) && $storeSetting->events['use_free_three_product']) {
            if ($this->productCount === 0)
                return;
            if ($this->productCount < 3) {
                foreach ($cart as &$product)
                    $product['is_free'] = false;
            } else {
                $min_price = null;

                foreach ($cart as $product) {
                    if (!in_array($product['id'], $exception_products)) {
                        $db_product = Product::find($product['id']);
                        if ($min_price === null || $db_product->getDiscountedPrice() < $min_price) {
                            $min_price = $db_product->getDiscountedPrice();
                        }
                    }
                }

                $is_free_set = false;
                foreach ($cart as &$product) {
                    $db_product = Product::find($product['id']);
                    if ($db_product->getDiscountedPrice() == $min_price && !$is_free_set && $product['type'] !== Product::TYPE_CERTIFICATE) {
                        $product['is_free'] = true;
                        $is_free_set = true;
                    } else
                        $product['is_free'] = false;
                }
            }
        }

//        $action = Action::first();
//        if ($action) {
//            // Костыль для акции 1руб.
//            $relatedProducts = $action['products_related_ids'];
//            $exceptionProducts = $action['products_exclude_ids'];
//
//            foreach ($cart as &$product) {
//                if (in_array($product['id'], $exceptionProducts)) {
//                    continue;
//                }
//                if (!in_array($product['id'], $relatedProducts)) {
//                    $pr = Product::find($relatedProducts[0]);
//                    if(!$pr) continue;
//                    $cart[] = [
//                        'id' => $pr->id,
//                        'name' => $pr->name,
//                        'variant' => $pr->variants()->first()->id,
//                        'price' => 1,
//                        'quantity' => 1,
//                        'type' => $pr->type,
//                        'is_free' => false,
//                        'selected' => true,
//                    ];
//                }
//            }
//        }
        session()->put("cart", $cart);
        $this->render();
    }

    public
    function mount()
    {
        $this->updateSelectAll();
        $this->onCartUpdate();
    }

    public
    function updateSelectAll()
    {
        $selectAll = true;
        $cart = session()->get('cart', []);
        foreach ($cart as $productId => $product) {
            if (!isset($product['selected'])) {
                $selectAll = false;
                break;
            }
            if (!$product['selected']) {
                $selectAll = false;
                break;
            }
        }
        $this->selectAll = $selectAll;
    }

    public
    function render()
    {
        $cart = session()->get('cart', []);
        return view('livewire.cart', ['cart' => $cart, 'productCount' => $this->productCount]);
    }
}
