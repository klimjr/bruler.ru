<?php

namespace App\Livewire\Order;

use App\Http\Controllers\CDEKController;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Cdek extends Component
{
    #[Reactive]
    public $addressCdek;
    public $type;
    public $products;

    #[Reactive]
    public $cityCode;
    public $country = 'RU';

    public function mount($type = null, $products = [])
    {
        $this->type = $type ?? 'cdek';
        $this->products = $products;
//        $this->getDeliveryPrice();
    }

    public function render()
    {
        return view('livewire.order.cdek');
    }

    #[On('get-delivery-price')]
    public function setDeliveryPrice()
    {
        $this->getDeliveryPrice();
    }

    #[On('reset-params')]
    public function resetParams()
    {
    }


    private function getDeliveryPrice()
    {
        $controller = new CDEKController();
        $packages = [];
        foreach ($this->products as $product) {
            if ($product['type'] == 'set') {
                foreach ($product['set_products'] as $productInSet) {
                    $variant = ProductVariant::find($productInSet[0]['selectedVariant']);

                    $packages[] = [
                        'weight' => $variant['weight'],
                        'length' => $variant['length'],
                        'width' => $variant['width'],
                        'height' => $variant['height'],
                    ];
                }
            } else {
                $variant = ProductVariant::find($product['variant']);

                $packages[] = [
                    'weight' => $variant['weight'],
                    'length' => $variant['length'],
                    'width' => $variant['width'],
                    'height' => $variant['height'],
                ];
            }
        }
        if($this->addressCdek == null) {
            return;
        }
        $result = $controller->calculate($this->cityCode, $this->addressCdek, $this->country, $packages);
        if (is_null($result) || !isset($result->tariff_codes))
            return;
        $codes = $result?->tariff_codes;
        foreach ($codes as $code) {
            // Посылка склад-склад
            /*if ($code->tariff_code == 136) {
                $code136 = $code;
            }*/
//            dump($code->tariff_code);
            // Посылка склад-дверь
            if ($code->tariff_code == 137) {
                $code137 = $code;
            }
        }
        $this->dispatch('set-delivery-price', price: $code137->delivery_sum);
    }
}
