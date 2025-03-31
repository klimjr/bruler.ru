<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PointsRedesign extends Component
{
    public $useBonus = false;
    public $bonus = null;
    public $isActive = false;
    public $message = '';
    public $error = false;
    public $haveSaleProducts = false;
    public $totalCart = 0;

    public function mount()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $product) {
            $product_db = Product::find($product['id']);
            if (isset($product_db['discount']) && $product_db['discount'] > 0) {
                $this->haveSaleProducts = true;
            }
        }
        $this->useBonus = Session::get('useBonus', false);
        $this->bonus = Session::get('bonus');

        if ($this->useBonus) {
            $this->applyBonus();
        }
    }

    public function applyBonus()
    {
        $totalDiscounted = 0;
        $userPoints = Auth::user()->points;
        $applyPoints = 0;
        $cart = session()->get('cart', []);

        foreach ($cart as $productOrder) {
            $db_product = Product::find($productOrder['id']);

            if (isset($db_product->discount) && $db_product->discount > 0) {
                $totalDiscounted += $db_product->getDiscountedPrice() * $productOrder['quantity'];
            }
        }

        $maxSum = round(($this->totalCart - $totalDiscounted) * (99 / 100));

        if ($this->bonus) {
            $applyPoints = $this->bonus;
        } else {
            if ($userPoints > $maxSum) {
                $applyPoints = $maxSum;
            } else {
                $applyPoints = $userPoints;
            }
        }

        if ($applyPoints && $applyPoints > 0) {
            $this->useBonus = true;
            $this->bonus = $applyPoints;

            Session::put('useBonus', $this->useBonus);
            Session::put('bonus', $this->bonus);
            $this->dispatch('bonusApply', $this->bonus);
        }
        // $this->resetErrorBag('bonus');
    }

    public function resetBonus()
    {
        $this->useBonus = false;
        $this->bonus = null;
        Session::forget(['useBonus', 'bonus']);
        $this->dispatch('bonusApply', 0);
    }

    public function render()
    {
        return view('livewire.points-redesign');
    }
}
