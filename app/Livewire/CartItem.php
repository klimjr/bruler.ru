<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{
    public $product;
    public $quantity;
    public $variant;
    public $newVariant = '';
    public $selected;
    public $type;
    public $is_free;
    public $certificate;
    public $index;
    public $set_products;
    public $cart_id;
    public $maxQuantity;

    public function mount($product, $quantity, $type, $variant, $certificate, $set_products, $is_free)
    {
        $this->product = Product::find($product['id']);
        $this->quantity = $quantity;
        $this->type = $type ?? Product::TYPE_PRODUCT;
        $this->is_free = $is_free ?? false;
        $this->variant = isset($variant) ? ProductVariant::find($variant) : null;
        $this->certificate = $certificate ?? null;
        $this->set_products = $set_products ?? null;
        $this->selected = $product['selected'] ?? false;
        $this->cart_id = isset($variant) ? $product['id'] . '_' . $variant : $product['id'] . '_' . $certificate['price'];
        $this->maxQuantity = $this->variant->amount ?? null;
    }

    #[On('cartUpdated')]
    public function justUpdateCart()
    {
        $this->updateCart();
        $this->dispatch('updateCartInCartItem');
    }
    public function incrementQuantity()
    {
        if ($this->quantity < $this->maxQuantity) {
            $data = explode("_", $this->cart_id);
            $cart = session()->get("cart", []);
            $this->quantity++;

            foreach ($cart as $ind => $item) {
                if ($item['id'] == $data[0]) {
                    if (
                        ($item['type'] == Product::TYPE_PRODUCT && $item['variant'] == $data[1]) ||
                        ($item['type'] == Product::TYPE_CERTIFICATE && $item['certificate']['price'] == $data[1])
                    ) {
                        if (!$cart[$ind]["selected"]) {
                            $cart[$ind]["is_free"] = false;
                            $this->is_free = false;
                        }

                        $cart[$ind]["quantity"]++;
                        session()->put("cart", $cart);
                    }
                }
            }
            $this->dispatch("totalCartUpdated");
            $this->updateCart();
            $this->dispatch('updateCartInCartItem');
        }
    }
    public function decrementQuantity()
    {
        $data = explode("_", $this->cart_id);
        $cart = session()->get("cart");

        foreach ($cart as $ind => $item) {
            if ($item['id'] == $data[0]) {
                if (
                    ($item['type'] == Product::TYPE_PRODUCT && $item['variant'] == $data[1]) ||
                    ($item['type'] == Product::TYPE_CERTIFICATE && $item['certificate']['price'] == $data[1])
                ) {
                    if (!$cart[$ind]["selected"]) {
                        $cart[$ind]["is_free"] = false;
                        $this->is_free = false;
                    }

                    if ($this->quantity >= 1) {
                        $this->quantity--;
                        if ($this->quantity === 0)
                            unset($cart[$ind]);
                        else
                            $cart[$ind]["quantity"]--;
                    } else
                        unset($cart[$ind]);

                    session()->put("cart", $cart);
                }
            }
        }
        $this->dispatch("totalCartUpdated");
        $this->updateCart();
        $this->dispatch('updateCartInCartItem');
    }

    public function changeSelected()
    {
        $cart = session()->get("cart");
        $cart[$this->index]['selected'] = $this->selected ?? false;
        session()->put("cart", $cart);
        $this->selected = $cart[$this->index]['selected'];
        $this->dispatch("cartUpdated");
    }

    public function changeVariant()
    {
        if ($this->newVariant) {
            $cart = session()->get("cart");
            $cart[$this->index]['variant'] = $this->newVariant;
            session()->put("cart", $cart);
            $this->variant = ProductVariant::find($cart[$this->index]['variant']);
            $this->dispatch("cartUpdated");
        }
    }

    #[On('productVariantUpdated')]
    public function selectNewVariant($productVariant)
    {
        $this->newVariant = $productVariant;
    }

    #[On('cartSelectAllUpdated')]
    public function onCartSelectAllUpdate($selectAll)
    {
        $this->selected = $selectAll;
        $this->changeSelected();
    }

    #[On('updateCartInCartItem')]
    public function updateCartInCartItem()
    {
        $cart = session()->get("cart", []);
        $this->is_free = $cart[$this->index]['is_free'] ?? false;
    }

    public function removeProduct($index)
    {
        $data = explode("_", $index);
        $cart = session()->get("cart");
        foreach ($cart as $ind => $item) {
            if ($item['id'] == $data[0]) {
                if (
                    ($item['type'] == Product::TYPE_PRODUCT && $item['variant'] == $data[1]) ||
                    ($item['type'] == Product::TYPE_CERTIFICATE && $item['certificate']['price'] == $data[1])
                ) {

                    unset($cart[$ind]);
                    break;
                }
            }
        }
        session()->put("cart", $cart);
        $this->dispatch("totalCartUpdated");
        // $this->dispatch("cartUpdated");
    }

    private function updateCart()
    {
        $this->productCount = 0;
        $cart = session()->get('cart', []);

        $storeSetting = StoreSetting::first();
        $exception_products = [];

        if (count($cart) !== 0) {
            foreach ($cart as $item) {
                if ($item['type'] === Product::TYPE_CERTIFICATE)
                    continue;

                $this->productCount += $item['quantity'];
            }
        }

        if (isset($storeSetting) && $storeSetting->events['use_free_three_product']) {
            if ($this->productCount === 0)
                return;
            if ($this->productCount < 3) {
                $this->is_free = false;
                foreach ($cart as &$product)
                    $product['is_free'] = false;
            } else {
                $min_price = null;

                foreach ($cart as $product) {
                    if (!in_array($product['id'], $exception_products)) {
                        if ($min_price === null || $product['price'] < $min_price) {
                            $min_price = $product['price'];
                        }
                    }
                }
                $is_free_set = false;
                foreach ($cart as &$product) {
                    if ($product['price'] == $min_price && !$is_free_set && $product['type'] !== \App\Models\Product::TYPE_CERTIFICATE) {
                        $product['is_free'] = true;
                        $is_free_set = true;
                    } else
                        $product['is_free'] = false;
                }
            }
        }
        session()->put("cart", $cart);
    }

    public function render()
    {
        return view("livewire.cart-item", [
            "product" => $this->product,
            "quantity" => $this->quantity,
        ]);
    }
}
