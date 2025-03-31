<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Promocode;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use Livewire\Attributes\On;

class PromocodeCheck extends Component
{
    public $code = '';
    public $isActive = false;
    public $message = '';
    public $error = false;

    protected $listeners = [
        'applyEvent' => 'applyCode',
        'resetEvent' => 'resetCode',
        'applyError' => 'showError'
    ];

    public function applyGlobal()
    {
        $this->dispatch('applyEvent', $this->code);
    }

    public function resetGlobal()
    {
        $this->dispatch('resetEvent');
    }

    public function showError($data)
    {
        $this->error = $data['error'];
        $this->message = $data['message'];

        if ($this->error) {
            $this->isActive = false;
        } else {
            $this->isActive = true;
            Session::put('promocode', $this->code);
            Session::put('isActive', $this->isActive);
            Session::put('message', $this->message);
        }
    }

    #[On('totalCartUpdated')]
    public function mount()
    {
        $this->code = Session::get('promocode', '');
        $this->isActive = Session::get('isActive', false);
        $this->message = Session::get('message', '');

        if ($this->code) {
            $this->applyCode($this->code);
        }
    }

    public function applyCode($code)
    {
        $cart = session()->get('cart', []);
        $productsWithoutSaleAmount = 0;
        $validProducts = 0;

        $promocode = Promocode::where([['code', $code], ['active', true]])->first();

        if (!$promocode) {
            $this->isActive = false;
            $this->error = true;
            $this->message = 'Неверный промокод';
            return;
        }

        // Преобразуем строку applicable_products в массив, если это строка
        $applicableProducts = $promocode->applicable_products;
        if (is_string($applicableProducts)) {
            $applicableProducts = array_filter(explode(',', $applicableProducts));
            // Преобразуем строковые ID в целые числа
            $applicableProducts = array_map('intval', $applicableProducts);
        }

        foreach ($cart as $item) {
            $db_product = Product::find($item['id']);
            if (!$db_product) continue;

            // Пропускаем товары с существующей скидкой
            if ($db_product->discount && $db_product->discount > 0) continue;

            $productsWithoutSaleAmount++;

            // Проверяем, применим ли промокод к данному товару
            if ($promocode->applies_to_all_products ||
                ($applicableProducts && in_array($db_product->id, $applicableProducts))) {
                $validProducts++;
            }
        }

        if ($productsWithoutSaleAmount == 0) {
            $this->isActive = false;
            $this->error = true;
            $this->message = 'Нельзя использовать промокод на товарах из категории Sale';
            return;
        }

        if ($validProducts == 0) {
            $this->isActive = false;
            $this->error = true;
            $this->message = 'Промокод не может быть применен к товарам в корзине';
            return;
        }

        $this->code = $code;
        $this->error = false;
        $this->dispatch('promocodeApply', $promocode);
    }

    public function resetCode()
    {
        $this->code = '';
        $this->isActive = false;
        $this->message = '';
        $this->error = false;
        $promocode = [];
        $this->dispatch('promocodeApply', $promocode);

        Session::forget(['promocode', 'isActive', 'message']);
    }

    public function render()
    {
        return view('livewire.promocode-check');
    }
}
