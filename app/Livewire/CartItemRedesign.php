<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItemRedesign extends Component
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
    public $isFavourite = false;

//    protected $listeners = ['favouriteStatusUpdated' => 'updateFavouriteStatus'];

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

        $this->checkFavouriteStatus();
    }

    private function checkFavouriteStatus()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->isFavourite = $this->product->favouritesBy()->where('user_id', $user->id)->exists();
        } else {
            $favourites = session()->get('favourites', []);
            $this->isFavourite = in_array($this->product->id, $favourites);
        }
    }

//    #[On('favouritesUpdated')]
//    public function updateFavouriteStatus($productId = null, $isFavourite = null)
//    {
//        if ($productId === null || $isFavourite === null) {
//            $this->checkFavouriteStatus();
//        } elseif ($this->product->id === $productId) {
//            $this->isFavourite = $isFavourite;
//        }
//    }

    #[On('cartUpdated')]
    public function justUpdateCart()
    {
        $this->updateCart();
        $this->dispatch('updateCartInCartItem');
    }

    public function incrementQuantity()
    {
        if ($this->quantity < $this->maxQuantity) {
            $this->quantity++;
            $this->updateCartItem(function (&$item) {
                $item['quantity']++;
                if (!$item['selected']) {
                    $item['is_free'] = false;
                    $this->is_free = false;
                }
            });
            $this->dispatch("totalCartUpdated");
            $this->updateCart();
            $this->dispatch('updateCartInCartItem');
        }
    }


    public function decrementQuantity()
    {
        if ($this->quantity > 0) {
            $this->quantity--;
            $this->updateCartItem(function (&$item, &$cart, $index) {
                if ($this->quantity === 0) {
                    unset($cart[$index]);
                } else {
                    $item['quantity']--;
                }
            });
            $this->dispatch("totalCartUpdated");
            $this->updateCart();
            $this->dispatch('updateCartInCartItem');
        }
    }


    public function removeProduct($index)
    {
        $this->updateCartItem(function (&$item, &$cart, $index) {
            unset($cart[$index]);
        });
        $this->dispatch("totalCartUpdated");
    }

    private function updateCartItem(callable $callback)
    {
        $data = explode("_", $this->cart_id);
        $cart = session()->get("cart", []);

        foreach ($cart as $index => &$item) {
            if ($item['id'] == $data[0]) {
                if (
                    ($item['type'] == Product::TYPE_PRODUCT && $item['variant'] == $data[1]) ||
                    ($item['type'] == Product::TYPE_CERTIFICATE && $item['certificate']['price'] == $data[1])
                ) {
                    // Выполнение переданной функции обратного вызова
                    $callback($item, $cart, $index);
                    break;
                }
            }
        }

        session()->put("cart", $cart);
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
        return view("livewire.cart-item-redesign", [
            "product" => $this->product,
            "quantity" => $this->quantity,
        ]);
    }
}
