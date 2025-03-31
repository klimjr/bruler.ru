<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\StoreSetting;
use App\Models\Order;

class TotalCartRedesign extends Component
{
    // Свойства для расчетов корзины
    public $totalCart;
    public $cart;

    // Свойства для работы с промокодом
    public $code;
    public $isActive = false;
    public $error = false;
    public $message = "";

    // Другие поля для скидок и акций
    public $onePlusOneSale = 0;
    public $saleBruler = 0;
    public $totalWithoutDiscountSum = 0;
    public $totalDiscountSum = 0;
    public $saleBonusPromoCert = 0;
    public $promocode;
    public $bonus;
    public $productCount = 0;
    public $certInCart = false;
    public $showSetAndProductError = false;
    public $showCertAndProductError = false;
    public $isDisable = false;
    public $priceWithoutDiscount = 0;
    public $salePrice = 0;

    // Прослушиватель событий
    protected $listeners = [
        "bonusApply" => "checkBonus",
        "cartUpdated" => "onCartUpdate",
        "totalCartUpdated" => "onCartUpdate",
    ];

    /**
     * Метод инициализации компонента.
     * Загружает промокод и корзину из сессии, а также выполняет первичный расчет.
     */
    public function mount()
    {
        // Инициализация данных промокода из сессии
        $this->code = session()->get("promocode", "");
        $this->isActive = session()->get("isActive", false);
        $this->message = session()->get("message", "");

        // Инициализация корзины
        $this->cart = session()->get("cart", []);
        $this->totalCart = $this->calculateTotal();

        if ($this->code) {
            $this->applyCode($this->code);
        }
    }

    /**
     * Метод расчета итоговой суммы корзины с учетом акций, скидок и промокода.
     */
    public function calculateTotal()
    {
        $cart = session()->get("cart", []);

        $total = 0;
        $this->totalWithoutDiscountSum = 0;
        $this->totalDiscountSum = 0;
        $this->onePlusOneSale = 0;
        $this->saleBruler = 0;
        $this->salePrice = 0;
        $this->saleBonusPromoCert = 0;
        $this->productCount = 0;
        $this->certInCart = false;
        $this->priceWithoutDiscount = 0;

        $storeSettings = StoreSetting::first();

        foreach ($cart as $product) {
            $this->productCount += $product["quantity"];

            // Пропускаем невыбранные товары
            if (!($product["selected"] ?? false)) {
                continue;
            }

            switch ($product["type"]) {
                case Product::TYPE_PRODUCT:
                case Product::TYPE_SET:
                    $db_product = Product::find($product["id"]);
                    $this->totalWithoutDiscountSum +=
                        $product["price"] * $product["quantity"];

                    if (
                        isset($db_product->discount) &&
                        $db_product->discount > 0
                    ) {
                        $this->saleBruler +=
                            ($product["price"] -
                                $db_product->getDiscountedPrice()) *
                            $product["quantity"];
                        $this->salePrice = $db_product->getDiscountedPrice();
                    }

                    if (
                        isset($product["is_free"]) &&
                        $product["is_free"] &&
                        isset($storeSettings) &&
                        $storeSettings->events["use_free_three_product"]
                    ) {
                        if ($product["quantity"] === 1) {
                            $total += 0;
                        } else {
                            $price = isset($db_product->discount)
                                ? $db_product->getDiscountedPrice()
                                : $product["price"];
                            $total += $price * ($product["quantity"] - 1);
                            $this->onePlusOneSale = $price;
                        }
                    } else {
                        if (
                            isset($db_product->discount) &&
                            $db_product->discount > 0
                        ) {
                            $total +=
                                $db_product->getDiscountedPrice() *
                                $product["quantity"];
                        } else {
                            $total += $product["price"] * $product["quantity"];
                        }
                    }
                    if ($db_product->discount == 0) {
                        $this->priceWithoutDiscount +=
                            $product["price"] * $product["quantity"];
                    }
                    break;

                case Product::TYPE_CERTIFICATE:
                    $this->certInCart = true;
                    $this->totalWithoutDiscountSum +=
                        $product["certificate"]["price"] * $product["quantity"];
                    $total +=
                        $product["certificate"]["price"] * $product["quantity"];
                    break;
            }
        }
        // Применяем бонус, если он есть
        if ($this->bonus) {
            $total -= $this->bonus;
            $this->saleBonusPromoCert += $this->bonus;
        }

        // Применяем скидку промокода, если она установлена (предполагается, что $this->promocode – процент скидки)
        if ($this->promocode) {
            $discountRate = $this->promocode / 100;
            $discountableAmount = 0;

            $cart = session()->get('cart', []);
            $promocodeObj = Promocode::where('discount', $this->promocode)->first();

            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if (!$product || ($product->discount && $product->discount > 0)) continue;

                if ($promocodeObj->applies_to_all_products ||
                    ($promocodeObj->applicable_products && in_array($product->id, $promocodeObj->applicable_products))) {
                    $itemPrice = $item['price'] * $item['quantity'];
                    $discountableAmount += $itemPrice;
                }
            }

            if ($this->bonus) {
                $discountAmount = ($discountableAmount - $this->bonus) * $discountRate;
            } else {
                $discountAmount = $discountableAmount * $discountRate;
            }

            $total -= $discountAmount;
            $this->totalDiscountSum += $discountAmount;
            $this->saleBonusPromoCert += $discountAmount;
        }



        $this->totalDiscountSum = $this->totalWithoutDiscountSum - $total;

        return $total;
    }

    /**
     * Обработчик события применения промокода.
     * Принимает промокод с процентной скидкой и обновляет итоговую сумму.
     */
    public function checkPromocode($promocode)
    {
        if (isset($promocode["discount"])) {
            // Если действует акция "1+1", промокод применять нельзя
            if ($this->onePlusOneSale > 0) {
                $this->showError([
                    "error" => true,
                    "message" =>
                        "Нельзя использовать промокод вместе с другими акциями",
                ]);

                return;
            }
            $this->promocode = $promocode["discount"];
            $this->totalCart = $this->calculateTotal();

            $this->showError([
                "error" => false,
                "message" => "Промокод активирован",
            ]);
        }
    }

    public function showError($data)
    {
        $this->error = $data["error"];
        $this->message = $data["message"];

        if ($this->error) {
            $this->isActive = false;
        } else {
            $this->isActive = true;
            Session::put("promocode", $this->code);
            Session::put("isActive", $this->isActive);
            Session::put("message", $this->message);
        }
    }

    /**
     * Обработчик применения бонуса.
     */
    #[On("bonusApply")]
    public function checkBonus($bonusAmount)
    {
        $this->bonus = $bonusAmount;
        $this->totalCart = $this->calculateTotal();
    }

    /**
     * Обработчик обновления корзины.
     */
    public function onCartUpdate()
    {
        $this->totalCart = $this->calculateTotal();
        $this->render();
    }

    /**
     * Создает заказ на основе выбранных продуктов.
     */
    public function createOrder($oneClick = 0)
    {
        $cart = session()->get("cart", []);
        $selectedProducts = [];
        $types = [];

        foreach ($cart as $product) {
            if (!($product["selected"] ?? false)) {
                continue;
            }
            $db_product = Product::find($product["id"]);
            $product["price"] = $db_product->getDiscountedPrice();
            $selectedProducts[] = $product;
            $types[] = $product["type"];
        }

        if (count($selectedProducts) < 1) {
            return 0;
        }

        if (
            in_array(Product::TYPE_SET, $types) &&
            in_array(Product::TYPE_PRODUCT, $types)
        ) {
            $this->showSetAndProductError = true;
            return 0;
        }

        if (
            in_array(Product::TYPE_CERTIFICATE, $types) &&
            in_array(Product::TYPE_PRODUCT, $types)
        ) {
            $this->showCertAndProductError = true;
            return 0;
        }

        if ($this->isDisable) {
            return;
        }

        $this->isDisable = true;
        $order = new Order();
        $order->products = $selectedProducts;
        $order->save();

        // Сохраняем id заказа в сессии
        session()->put("order_id", $order->id);

        if ($oneClick == 1 && !auth()->check()) {
            return redirect()->route("order", ["order_without_auth" => 1]);
        } else {
            return redirect()->route("order");
        }
    }

    /**
     * Метод проверки и применения промокода.
     * Реализует логику валидации промокода по товарам корзины.
     */
    public function applyCode()
    {
        $code = $this->code;

        $cart = session()->get("cart", []);
        $productsWithoutSaleAmount = 0;
        foreach ($cart as $item) {
            $db_product = Product::find($item["id"]);
            if (
                $db_product &&
                (!$db_product->discount || $db_product->discount == 0)
            ) {
                $productsWithoutSaleAmount++;
            }
        }

        if ($productsWithoutSaleAmount == 0) {
            $this->isActive = false;
            $this->error = true;
            $this->message =
                "Нельзя использовать промокод на товарах из категории Sale";
            return;
        }

        $promocode = Promocode::where([
            ["code", $code],
            ["active", true],
        ])->first();

        if ($promocode) {
            $this->code = $code;
            $this->error = false;
            $this->checkPromocode($promocode);
        } else {
            $this->isActive = false;
            $this->error = true;
            $this->message = "Неверный промокод";
        }
    }

    /**
     * Сбрасывает состояние промокода.
     */
    public function resetCode()
    {
        $this->code = "";
        $this->isActive = false;
        $this->message = "";
        $this->error = false;
        $this->promocode = null;
        $this->checkPromocode([]);
        session()->forget(["promocode", "isActive", "message"]);
        $this->onCartUpdate();
    }

    public function render()
    {
        return view("livewire.total-cart-redesign");
    }
}
