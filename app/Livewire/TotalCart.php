<?php

namespace App\Livewire;

use App\Models\StoreSetting;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TotalCart extends Component
{
    public $totalCart;
    public $promocode;
    public $bonus = 0;
    public $totalDiscountSum;
    public $onePlusOneSale;
    public $saleBruler;
    public $saleBonusPromoCert;
    public $totalWithoutDiscountSum;
    public $productCount;
    public $isDisable = false;
    public $showCertAndProductError = false;
    public $showSetAndProductError = false;
    public $certInCart = false;

    public function mount()
    {
        $this->totalCart = $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $cart = session()->get('cart', []);

        $total = 0;
        $this->totalWithoutDiscountSum = 0;
        $this->totalDiscountSum = 0;
        $this->onePlusOneSale = 0;
        $this->saleBruler = 0;
        $this->saleBonusPromoCert = 0;
        $this->productCount = 0;

        $storeSettings = StoreSetting::first();

        foreach ($cart as $product) {
            $this->productCount += $product['quantity'];
            if (!$product['selected'] ?? false)
                continue;

            switch ($product['type']) {
                case \App\Models\Product::TYPE_PRODUCT:
                case \App\Models\Product::TYPE_SET:
                    $db_product = \App\Models\Product::find($product['id']);
                    $this->totalWithoutDiscountSum += $product['price'] * $product['quantity'];

                    if (isset($db_product->discount) && $db_product->discount > 0) {
                        $this->saleBruler += ($product['price'] - $db_product->getDiscountedPrice()) * $product['quantity'];
                    }

                    if (isset($product['is_free']) && $product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product']) {
                        if ($product['quantity'] === 1) {
                            $total += 0;
                        } else {
                            $total += isset($db_product->discount)
                                ? $db_product->getDiscountedPrice() * ($product['quantity'] - 1)
                                : $product['price'] * ($product['quantity'] - 1);
                        }
                        $this->onePlusOneSale = isset($db_product->discount)
                            ? $db_product->getDiscountedPrice()
                            : $product['price'];
                    } else {
                        if (isset($db_product->discount) && $db_product->discount > 0) {
                            $total += $db_product->getDiscountedPrice() * $product['quantity'];
                        } else {
                            $total += $product['price'] * $product['quantity'];

                            if ($this->promocode) {
                                $discountRate = $this->promocode / 100;
                                $discountAmount = $product['price'] * $product['quantity'] * $discountRate;

                                $total -= $discountAmount;
                                $this->totalDiscountSum += $discountAmount;
                                $this->saleBonusPromoCert += $discountAmount;
                            }
                        }
                    }
                    break;

                case \App\Models\Product::TYPE_CERTIFICATE:
                    $this->certInCart = true;
                    $this->totalWithoutDiscountSum += $product['certificate']['price'] * $product['quantity'];
                    $total += $product['certificate']['price'] * $product['quantity'];
                    break;
            }
        }

        if ($this->bonus) {
            $total -= $this->bonus;
            $this->saleBonusPromoCert += $this->bonus;
        }

        $this->totalDiscountSum = $this->totalWithoutDiscountSum - $total;

        return $total;
    }

    #[On('promocodeApply')]
    public function checkPromocode($promocode)
    {
        if (isset($promocode['discount'])) {
            if ($this->onePlusOneSale > 0) {
                $this->dispatch('applyError', [
                    'error' => true,
                    'message' => 'Нельзя использовать промокод вместе с другими акциями'
                ]);
                return;
            }

            $this->promocode = $promocode['discount'];
            $this->totalCart = $this->calculateTotal();

            $this->dispatch('applyError', [
                'error' => false,
                'message' => 'Промокод активирован'
            ]);
        }
    }

    #[On('bonusApply')]
    public function checkBonus($bonusAmount)
    {
        $this->bonus = $bonusAmount;
        $this->totalCart = $this->calculateTotal();
    }

    #[On('cartUpdated')]
    #[On('totalCartUpdated')]
    public function onCartUpdate()
    {
        $this->totalCart = $this->calculateTotal();
        $this->render();
    }

    public function createOrder($oneClick = 0)
    {
        $cart = session()->get('cart', []);
        $selectedProducts = [];
        $types = [];
        foreach ($cart as $productId => $product) {
            if (!$product['selected'] ?? false)
                continue;
            $db_product = \App\Models\Product::find($product['id']);
            $product['price'] = $db_product->getDiscountedPrice();
            $selectedProducts[] = $product;
            $type = $product['type'];
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }
        if (count($selectedProducts) < 1)
            return 0; // если выбранные продукты пустые, то ретурн

        if (in_array('set', $types) && in_array('product', $types)) {
            $this->showSetAndProductError = true;
            return 0;
        }

        if (in_array('certificate', $types) && in_array('product', $types)) {
            $this->showCertAndProductError = true;
            return 0;
        }

        if ($this->isDisable) {
            return $this->isDisable = true;
        }

        // Создание лиде
        $lead = new \App\Models\Order();
        $lead->products = $selectedProducts;
        $lead->save();

        // save order id to session
        session()->put('order_id', $lead->id);

        if ($oneClick == 1 && !Auth::check()) {
            return redirect()->route('order', ['order_without_auth' => 1]);
        } else {
            return redirect()->route('order');
        }
    }

    public function render()
    {
        return view('livewire.total-cart');
    }
}
