<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;
    public $color = null;
    public $selectedVariant = null;
    public $selectedCertificate = null;
    public $setProducts = [];
    public $showErrorMessage = false;
    public $showErrorCertMessage = false;
    public $maxQuantity;

    #[On('productVariantUpdated')]
    public function onProductVariantUpdate($productVariant)
    {
        $this->selectedVariant = $productVariant;
        $this->maxQuantity = ProductVariant::find($this->selectedVariant)->amount ?? null;
    }

    #[On('productCertificateAdded')]
    public function onProductCertificateAdded($cert)
    {
        $this->product->price = $cert['price'];
        $this->selectedCertificate = $cert;
    }

    #[On('productSetUpdated')]
    public function onProductSetUpdate($products)
    {
        $this->setProducts = $products;
    }

    public function mount()
    {
        if ($this->product->type !== Product::TYPE_CERTIFICATE) {
            $this->color = $this->product->availableColors()->first()->id ?? null;
            $this->maxQuantity = ProductVariant::find($this->selectedVariant)->amount ?? null;
        }
    }

    public function isProductInCart()
    {
        return $this->getCartItem() !== null;
    }

    public function getProductQuantityInCart()
    {
        $item = $this->getCartItem();
        return $item['quantity'] ?? 0;
    }

    protected function getCartItem()
    {
        $cart = session()->get('cart', []);
        foreach ($cart as $item) {
            if ($item['id'] === $this->product->id) {
                if (
                    ($this->product->type === Product::TYPE_PRODUCT && $item['variant'] == $this->selectedVariant) ||
                    ($this->product->type === Product::TYPE_CERTIFICATE && $item['certificate'] == $this->selectedCertificate)
                ) {
                    return $item;
                }
            }
        }
        return null;
    }

    public function removeFromCart()
    {
        $cart = session()->get('cart', []);
        foreach ($cart as $index => &$item) {
            if ($item['id'] === $this->product->id && $this->matchesCartItem($item)) {
                $item['quantity'] -= 1;
                if ($item['quantity'] <= 0) {
                    unset($cart[$index]);
                } else {
                    $cart[$index] = $item;
                }
                session()->put('cart', $cart);
                $this->dispatch('cartUpdated');
                return 200;
            }
        }
        return 0;
    }

    public function handleAddToCart($data)
    {
        $this->product = Product::find($data['product_id']);
        $this->selectedVariant = $data['variant_id'] ?? null;
        $this->updateCart($data['quantity'] ?? 1);
    }

    public function addToCart()
    {
        if ($this->validateProductSelection()) {
            if ($this->product->type == 'certificate') {
                $this->updateCart(1);
                return 200;
            }

            if ($this->getProductQuantityInCart() < $this->maxQuantity) {
                $this->updateCart(1);
                return 200;
            }
        }
        return 0;
    }

    public function addToCartAndRedirect()
    {
        if ($this->addToCart() === 200) {
            $totalOrder = new TotalCart();
            $totalOrder->createOrder(1);
        }
    }

    protected function validateProductSelection()
    {
        $this->showErrorMessage = is_null($this->selectedVariant) && $this->product->type === Product::TYPE_PRODUCT;
        $this->showErrorCertMessage = is_null($this->selectedCertificate) && $this->product->type === Product::TYPE_CERTIFICATE;

        if ($this->product->type === Product::TYPE_SET && !$this->isValidSet()) {
            $this->showErrorMessage = true;
            return false;
        }
        return !$this->showErrorMessage && !$this->showErrorCertMessage;
    }

    protected function isValidSet()
    {
        foreach ($this->setProducts as $productInSet) {
            if (!isset($productInSet['selected']) || !isset($productInSet['selectedVariant'])) {
                return false;
            }
        }
        return true;
    }

    protected function updateCart($quantity)
    {
        $cart = [];
        if ($this->product->type != 'certificate') {
            $cart = session()->get('cart', []);
            foreach ($cart as $item) {
                if ($item['type'] == 'certificate') {
                    $cart = [];
                    break;
                }
            }
        }

        foreach ($cart as &$item) {
            if ($item['id'] == $this->product->id && $this->matchesCartItem($item)) {
                $item['quantity'] += $quantity;
                session()->put('cart', $cart);
                $this->dispatch('cartUpdated');
                return;
            }
        }
        $cart[] = $this->createCartItem($quantity);
//        $actions = $this->product->getActions();
//        if($actions) {
//            // Костыль для акции 1руб.
//            $action = $actions->first();
//            $relatedProducts = $action['products_related'];
//            $cartIds = \Arr::pluck($cart, 'id');
//            if (!empty(array_diff($relatedProducts, $cartIds))) {
//                foreach ($relatedProducts as $productId) {
//                    if (!in_array($productId, $cartIds)) {
//                        $pr = Product::find($productId);
//                        $cartItem = [
//                            'id' => $pr->id,
//                            'name' => $pr->name,
//                            'variant' => $pr->variants()->first()->id,
//                            'price' => 1, // настройки акции не работают
//                            'quantity' => 1,
//                            'type' => $pr->type,
//                            'is_free' => false,
//                            'selected' => true,
//                        ];
//                        $cart[] = $cartItem;
//                    }
//                }
//            }





        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
    }

    protected function matchesCartItem($item)
    {
        return (
            ($this->product->type === Product::TYPE_PRODUCT && $item['variant'] == $this->selectedVariant) ||
            ($this->product->type === Product::TYPE_CERTIFICATE && $item['certificate'] == $this->selectedCertificate)
        );
    }

    protected function createCartItem($quantity)
    {
        $cartItem = [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => $this->product->price,
            'quantity' => $quantity,
            'type' => $this->product->type,
            'is_free' => false,
            'selected' => true,
        ];
        if ($this->product->type === Product::TYPE_PRODUCT) {
            $cartItem['variant'] = $this->selectedVariant;
        } elseif ($this->product->type === Product::TYPE_CERTIFICATE) {
            $cartItem['price'] = $this->selectedCertificate['price'];
            $cartItem['certificate'] = $this->selectedCertificate;
        } elseif ($this->product->type === Product::TYPE_SET) {
            $cartItem['set_products'] = $this->setProducts;
        }
        return $cartItem;
    }

    public function render()
    {
        return view('livewire.add-to-cart', [
            'isProductInCart' => $this->isProductInCart(),
            'productQuantityInCart' => $this->getProductQuantityInCart(),
        ]);
    }
}
