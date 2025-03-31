<?php

namespace App\Livewire;

use App\Http\Controllers\CDEKController;
use App\Http\Controllers\DolyamiController;
use App\Http\Controllers\YandexPayController;
use App\Http\Controllers\TinkoffController;
use App\Jobs\CheckYandexPayStatus;
use App\Jobs\FetchCdekOrderInfo;
use App\Models\CdekPvz;
use App\Models\ProductVariant;
use App\Models\Promocode;
use App\Models\StoreSetting;
use App\Services\DostavistaApi;
use CdekSDK2\BaseTypes\Location;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Product;

class Order extends Component
{
    public string $orderType = Product::TYPE_PRODUCT;

    // User
    public string $name = "";
    public string $lastName = "";
    public string $email = "";
    public string|null $phone = "";
    public int $points = 0;
    public float $deliveryPrice = 0;
    public bool $acceptTerms = false;

    public string $comment = "";

    // Payment
    public array $paymentTypes;
    public $selectedPaymentType;

    // Delivery
    public array $deliveryTypes;
    public $selectedDeliveryType;
    public $countryName = "Россия";

    // Address
    public $country = "RU";
    public $city = "Москва";
    public $cityCode = 44; // Москва
    public $address = "";
    public $addressDostavista = "";
    public $addressCdek = "";
    public $pcs = 1;

    public $leadId;

    public $saleBruler = 0;
    public $bonus = 0;
    public $promocode;
    public $usePromocode = false;
    public $priceWithPromocode = 0;
    public $promocodeDiscount = 0;
    public $totalOrder = 0;

    public $useCertificate = false;
    public $certificate = "";
    public $certificateRemains = 0;

    public $cart;
    public $totalWithoutDiscountSum = 0;

    public $cdekPvzsList;
    public $addressPoint = "";
    public $tariffInfo = [];

    // TMP
    public $cityInput;
    public $cities = [];
    public $onePlusOneSale;
    public $cityGeoLat;
    public $cityGeoLon;
    public $addresses = [];
    public bool $isLoading = false;
    public $uuidCdek;
    public bool $productAmountError;
    public bool $useBonus = false;
    public $db_promocode;
    public $dostavistaOrder;
    public int|float $priceNotSaleBruler;
    public $totalDiscount;

    public $target_email;
    public $countWithoutSale = 0;
    public $certificateInCart;

    protected $messages = [
        'phone.regex' => 'Неверный формат номера телефона',
    ];

    public function mount()
    {
        // TODO: вынести в отдельный метод
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name ?? "";
            $this->lastName = $user->last_name ?? "";
            $this->email = $user->email ?? ""; // если через соц. сети, то email может быть пустым
            $this->phone = $user->phone ?? "(999) 999-9999";
            $this->points = $user->points ?? 0;
        }

        // Получаем корзину из сессии
        $this->cart = session()->get("cart", []);
        $this->bonus = session()->get("bonus", 0);

        // Если корзина пуста, то редиректим на главную
        if (count($this->cart) < 1) {
            return redirect()->route("home");
        }

        $products = Product::query()
            ->whereIn("id", \Arr::pluck($this->cart, "id"))
            ->get();

        foreach ($this->cart as $key => $item) {

            if ($item['type'] === \App\Models\Product::TYPE_CERTIFICATE) {
                $this->certificateInCart = $item;
            }

            $product = $products->where("id", $item["id"])->first();
            $this->cart[$key]["image"] = $product->image;
            if ($product->discount > 0) {
                $this->cart[$key]["price"] =
                    $product->price -
                    ($product->price / 100) * $product->discount;
                $this->cart[$key]["old_price"] = $product->price;
            } else {
                $this->countWithoutSale += 1;
            }
        }
        // Получаем типы доставки и устанавливаем первый активный
        $this->deliveryTypes = $this->getDeliveryTypes();
        $this->selectedDeliveryType = reset($this->deliveryTypes)["id"];
        if ($this->selectedDeliveryType == "dostavista") {
            $this->city = "Москва";
            //            $this->selectCity('Москва', '44', '55.755826', '37.6173');
        }
        // Получаем типы оплаты и устанавливаем первый активный
        $this->paymentTypes = $this->getPaymentTypes();
        $this->selectedPaymentType = $this->paymentTypes[0]["id"];

        $promo = session()->get("promocode", null);
        $p = \App\Models\Promocode::where("code", $promo)->first();
        if ($p) {
            $this->promocode = $p->code;
            $this->usePromocode = true;
            $this->promocodeDiscount = $p->discount;
        }

        $this->totalWithoutDiscountSum = $this->getTotalWithoutDiscountSum();
        $this->orderType = $this->getProductType();

        $this->saleBruler = $this->getSaleBruler();

        $this->priceNotSaleBruler = $this->getPriceNotSaleBruler();

        $this->addressDostavista = '';

        if ($this->orderType == \App\Models\Product::TYPE_PRODUCT) {
            $this->cityCode = 0;
            $this->getCdekPvzs();
        }

        $this->leadId = session()->get("order_id", null);

        //        $this->leadId = $this->createLead();
    }

    public function fillDeliveryInfo($codePoint, $addressPoint)
    {
        $this->addressPoint = $addressPoint;
        $type = "office";
        $data = [
            "type" => $type,
            "delivery" => $this->tariffInfo,
            "point" => [
                "id" => $codePoint,
                "address" => $addressPoint,
                "city" => $this->city,
            ],
        ];
        $this->deliveryPrice = $this->tariffInfo["delivery_sum"] ?? 0;
        $lead = \App\Models\Order::find($this->leadId);
        $lead->delivery_info = $data;

        $this->resetErrorBag();
        // TODO: разобраться с этим
        //$this->delivery_types[\App\Models\Order::DELIVERY_TYPE_CDEK_PVZ]['price'] = $this->tariffInfo['delivery_sum'];
        $lead->save();
    }

    public function selectAddress($address)
    {
        if ($this->selectedDeliveryType == "dostavista") {
            $this->addressDostavista = $address;
        }
        $this->address = $address;
        $this->addresses = [];
        $this->dispatch("address-update", $address);
    }

    public function onChangeAddress($address)
    {
        $this->address = $address;
        $this->addresses = [];
        $this->resetErrorBag();
        $this->deliveryPrice = 0;
        $this->dispatch("get-delivery-price");
    }

    public function cancelOrder()
    {
        $lead = \App\Models\Order::find($this->leadId);
        $lead->update([
            "status" => "canceled",
        ]);
        session()->forget("order_id");
        session()->forget("cart");
        return redirect()->route("home");
    }

    public function updated($field, $value)
    {
        $lead = \App\Models\Order::find($this->leadId);
        $lead->update([
            $field => $value,
        ]);
        if($field == "phone") {
            $this->dispatch("phone-update", $value);
        }
    }

    private function createLead()
    {
        $leadData = [
            "products" => $this->cart,
            "country" => $this->country,
            "city_code" => $this->cityCode,
            "price" => $this->totalOrder,
            "price_order" => $this->totalOrder,
            "type" => $this->orderType,
        ];

        $lead = new \App\Models\Order();
        $lead->fill($leadData);
        $lead->save();
        return $lead->id;
    }
    #[On("resetErrors")]
    public function resetErrors()
    {
        $this->resetErrorBag();
    }

    #[On("certificateCancel")]
    public function cancelCertificate()
    {
        $this->useCertificate = false;
        $this->certificate = "";
        $this->certificateRemains = 0;
    }

    #[On("certificateUsed")]
    public function useCertificate($certificate)
    {
        $this->useCertificate = true;
        $this->certificate = $certificate["certificate"];
        $this->certificateRemains = $certificate["remains"];
    }

    #[On("promocodeApply")]
    public function checkPromocode($promocode)
    {
        if (isset($promocode["code"])) {
            if ($this->onePlusOneSale > 0) {
                $this->dispatch("applyError", [
                    "error" => true,
                    "message" =>
                        "Нельзя использовать промокод вместе с другими акциями",
                ]);
                return;
            }

            $this->usePromocode = (bool)$promocode;
            $this->promocode =
                isset($promocode["code"]) && $promocode["code"]
                    ? $promocode["code"]
                    : null;
            $this->db_promocode = $promocode ?: null;
            $this->promocodeDiscount = $promocode["discount"] ?? 0;

            $this->dispatch("applyError", [
                "error" => false,
                "message" => "Промокод активирован",
            ]);
        } else {
            $this->usePromocode = (bool)$promocode;
            $this->promocode =
                isset($promocode["code"]) && $promocode["code"]
                    ? $promocode["code"]
                    : null;
            $this->db_promocode = $promocode ?: null;
        }

        //        $this->onChangeDeliveryType();
        //        $this->reRenderPrice();
    }

    public function applyBonus()
    {
        $totalDiscounted = 0;
        $totalWithoutDiscounted = 0;
        $userPoints = \Auth::user()->points;
        $applyPoints = 0;
        $cart = session()->get("cart", []);

        foreach ($cart as $productOrder) {
            $db_product = Product::find($productOrder["id"]);

            if (isset($db_product->discount) && $db_product->discount > 0) {
                $totalDiscounted +=
                    $db_product->getDiscountedPrice() *
                    $productOrder["quantity"];
            } else {
                $totalWithoutDiscounted +=
                    $db_product->price * $productOrder["quantity"];
            }
        }

        $maxSum = $totalWithoutDiscounted - $userPoints;

        if ($this->bonus) {
            $applyPoints = $this->bonus;
        } else {
            if ($userPoints > $maxSum) {
                $applyPoints = $totalWithoutDiscounted - 1;
            } else {
                $applyPoints = $userPoints;
            }
        }

        if ($applyPoints && $applyPoints > 0) {
            $this->useBonus = true;
            $this->bonus = $applyPoints;

            Session::put("useBonus", $this->useBonus);
            Session::put("bonus", $this->bonus);

            $this->dispatch("bonusApply", $this->bonus);
        }
    }

    public function resetBonus()
    {
        $this->useBonus = false;
        $this->bonus = null;
        Session::forget(["useBonus", "bonus"]);
        $this->dispatch("bonusApply", 0);
    }

    #[On("set-delivery-price")]
    public function setDeliveryPrice($price)
    {
        $this->deliveryPrice = $price;
    }

    #[On("create-dostavista-order")]
    public function createDostavistaOrder($order)
    {
        $this->dostavistaOrder = $order;
    }

    public function getCdekPvzs()
    {
        if (!$this->cityCode) {
            return;
        }

        $cdekPvzs = CdekPvz::query()
            ->where("city_code", $this->cityCode)
            ->get()
            ->toJson();

        $this->cdekPvzsList = $cdekPvzs;
        $this->getCdekTariff();
    }

    public function searchCdekApi()
    {
        $response = Http::cdek()->get("https://api.cdek.ru/v2/deliverypoints", [
            "city_code" => $this->cityCode,
            "type" => "PVZ",
        ]);

        $json = $response->json();
        $status = $response->status();

        if ($json && $status == 200) {
            foreach ($json as $value) {
                CdekPvz::create([
                    "code" => array_key_exists("code", $value)
                        ? $value["code"]
                        : "",
                    "city_code" => array_key_exists(
                        "city_code",
                        $value["location"]
                    )
                        ? $value["location"]["city_code"]
                        : "",
                    "address" => array_key_exists("address", $value["location"])
                        ? $value["location"]["address"]
                        : "",
                    "phones" => array_key_exists("phones", $value)
                        ? json_encode($value["phones"])
                        : "",
                    "work_time" => array_key_exists("work_time", $value)
                        ? $value["work_time"]
                        : "",
                    "is_dressing_room" => array_key_exists(
                        "is_dressing_room",
                        $value
                    )
                        ? $value["is_dressing_room"]
                        : 0,
                    "address_comment" => array_key_exists(
                        "address_comment",
                        $value
                    )
                        ? $value["address_comment"]
                        : "",
                    "location_latitude" => array_key_exists(
                        "latitude",
                        $value["location"]
                    )
                        ? $value["location"]["latitude"]
                        : "",
                    "location_longitude" => array_key_exists(
                        "longitude",
                        $value["location"]
                    )
                        ? $value["location"]["longitude"]
                        : "",
                ]);
            }

            $this->getCdekPvzs();
        }
    }

    public function getCdekTariff()
    {
        $packages = [];
        $order = \App\Models\Order::find(session()->get("order_id"));

        $emptyPackage = [
            "weight" => 1,
            "length" => 1,
            "width" => 1,
            "height" => 1,
        ];

        // TODO: Временно
        if ($order && !count($order->products)) {
            $packages[] = $emptyPackage;
            \Log::info("No products found", ["order_id" => $order->id]);
        }

        foreach ($order->products as $product) {
            if ($product["type"] == "set") {
                foreach ($product["set_products"] as $productInSet) {
                    $variant = ProductVariant::find(
                        $productInSet[0]["selectedVariant"]
                    );

                    $packages[] = [
                        "weight" => $variant["weight"],
                        "length" => $variant["length"],
                        "width" => $variant["width"],
                        "height" => $variant["height"],
                    ];
                }
            } else {
                if (isset($product["variant"])) {
                    $variant = ProductVariant::find($product["variant"]);
                    $packages[] = [
                        "weight" => $variant["weight"],
                        "length" => $variant["length"],
                        "width" => $variant["width"],
                        "height" => $variant["height"],
                    ];
                } else {
                    // TODO: Временное решение
                    $packages[] = $emptyPackage;

                    \Log::error("Product variant not found", [
                        "product_id" => $product["id"],
                    ]);
                }
            }
        }

        $from_location = Location::create([
            "code" => config("services.cdek.from.code"),
            "address" => config("services.cdek.from.address"),
            "country_code" => config("services.cdek.from.country_code"),
        ]);

        $to_location = Location::create([
            "code" => $this->cityCode,
            "address" => $this->address,
            "country_code" => $this->country,
        ]);

        $response = Http::cdek()->post(
            "https://api.cdek.ru/v2/calculator/tariff",
            [
                //            'date' => '2019-08-24T14:15:22Z',
                "tariff_code" => 137,
                "from_location" => $from_location,
                "to_location" => $to_location,
                "packages" => $packages,
            ]
        );

        $res = $response->json();
        $this->tariffInfo = $res;
    }

    public function updatingSelectedDeliveryType($value)
    {
        if ($this->selectedDeliveryType != $value) {
            $this->dispatch("reset-params");
        }
        switch ($value) {
            case "dostavista":
                $this->cityCode = 44;
                $this->city = "Москва";
                $this->cityInput = "Москва";
                $this->addressDostavista = "";
                $this->dostavistaOrder = null;
                $this->address = "";
                break;
            case "cdek_pvz":
            case "cdek":
                $this->cityCode = null;
                $this->city = "";
                $this->cityInput = "";
                $this->addressCdek = "";
                $this->addressPoint = "";
                $this->address = "";
                break;
        }
        return match ($value) {
            "pickup" => ($this->deliveryPrice = 0),
            default => ($this->deliveryPrice = 0),
        };
    }

    //    public function updatedBonuses($value)
    //    {
    //        if ($value > 0) {
    //            $this->saleBruler = $this->saleBruler + $this->points;
    //        } else {
    //            $this->saleBruler = $this->saleBruler - $this->points;
    //        }
    //
    //    }
    #[On("dostavista-error")]
    public function dostavistaError($message)
    {
        $this->addError("dostavista", $message);
    }

    public function createOrder()
    {

        $this->validate([
            "name" => "required",
            "lastName" => "required",
            "email" => "required|email",
            "phone" => [
                'required',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
                'min:11',
                'max:18'
            ],
            "acceptTerms" => "required|accepted",
        ]);

        if ($this->selectedDeliveryType == "dostavista" && $this->orderType == \App\Models\Product::TYPE_PRODUCT) {
            $this->validate([
                'dostavistaOrder' => 'required',
            ]);
        }

        if ($this->selectedDeliveryType == "cdek" && $this->orderType == \App\Models\Product::TYPE_PRODUCT) {
            $this->validate([
                'addressCdek' => 'required',
            ]);
        }

        if ($this->selectedDeliveryType == "cdek_pvz" && $this->orderType == \App\Models\Product::TYPE_PRODUCT) {
            $this->validate([
                "addressPoint" => "required",
            ]);
        }


        //        trap([
        //            'orderType' => $this->orderType,
        //            'selectedPaymentType' => $this->selectedPaymentType,
        //            'selectedDeliveryType' => $this->selectedDeliveryType,
        //            'name' => $this->name,
        //            'lastName' => $this->lastName,
        //            'email' => $this->email,
        //            'phone' => $this->phone,
        //            'comment' => $this->comment,
        //            'totalOrder' => $this->totalOrder,
        //            'acceptTerms' => $this->acceptTerms,
        //        ]);

        $order = \App\Models\Order::find(session()->get("order_id"));

        $products = $order->products;
        // Проверка наличия товаров
        $this->checkAvailability($products);


        // Создаем url успешной оплаты
        // TODO: проверить все данные
        $success_route = $this->getSuccessUrl($order);
        $failed_route = route("failed_order");

        $order->payment_url = $success_route;
        // Сохранение заказа
        $this->saveOrder($order);

        // Если конечная сумма с промокодами и сертификатами равна 0
        if ($order->price === 0) {
            $order->status = \App\Models\Order::STATUS_CONFIRMED;
            $order->confirmation_at = now();
            $order->save();
            return redirect($success_route);
        }

        // TODO: add constants
        switch ($this->selectedPaymentType) {
            case "cash":
                $this->payCash($order, $success_route);
                break;
            case "card":
                $this->payCard($order, $success_route, $failed_route);
                break;
            case "dolyami":
                $this->payDolyami($order, $success_route, $failed_route);
                break;
            case "yandex":
                $this->payYandex($order, $success_route, $failed_route);
                break;
            default:
                dd("Error! No payment method");
        }
    }

    private function getTotalWithoutDiscountSum()
    {
        $totalWithoutDiscountSum = 0;
        $products = Product::query()
            ->whereIn("id", \Arr::pluck($this->cart, "id"))
            ->get();
        foreach ($this->cart as $cartItem) {
            $product = $products->where("id", $cartItem["id"])->first();
            if ($cartItem["quantity"] > 1) {
                $totalWithoutDiscountSum +=
                    $product->price * $cartItem["quantity"];
            } else {
                $totalWithoutDiscountSum += $product->price;
            }
        }
        return $totalWithoutDiscountSum;
    }

    private function getPaymentTypes()
    {
        $storeSettings = StoreSetting::all();
        return collect(array_values($storeSettings[0]->events["payments"]))
            ->where("active", true)
            ->toArray();
    }

    private function getDeliveryTypes()
    {
        $storeSettings = StoreSetting::all();
        return collect(array_values($storeSettings[0]->events["delivery"]))
            ->where("active", true)
            ->toArray();
    }

    public function render()
    {
        $this->totalOrder = $this->getTotalSum();
        $products = $this->cart;
        $productsModel = Product::with("variants")
            ->whereIn("id", \Arr::pluck($products, "id"))
            ->get();
        foreach ($products as $index => $product) {
            $p = $productsModel->find($product["id"]);
            $products[$index]['name'] = $p->name_en ?? $p->name;
            if (isset($product["variant"]) && $product["variant"] != null) {
                $products[$index]['size'] = $p->variants()->where('id', $product["variant"])->first()->size()->first()->name;
            } else {
                $products[$index]['size'] = '-';
            }
        }
        return view("order", [
            "products" => $products, // TODO: DTO
        ])->extends("layouts.order");
    }

    private function getProductType()
    {
        $products = session()->get("cart", []);
        $hasCertificate = collect($products)->contains(
            "type",
            Product::TYPE_CERTIFICATE
        );
        return $hasCertificate
            ? Product::TYPE_CERTIFICATE
            : Product::TYPE_PRODUCT; // TODO: SETS???
    }

    public function selectCity($city, $code, $geoLat, $geoLon)
    {
        $this->city = $city;
        $this->cityInput = $city;
        $this->cityCode = $code;
        $this->cityGeoLat = $geoLat;
        $this->cityGeoLon = $geoLon;
        $this->cities = [];
        if($this->selectedDeliveryType == "cdek_pvz") {
            $this->getCdekPvzs();
        }
        if($this->selectedDeliveryType == "cdek") {
            $this->addressCdek = '';
        }

    }

    #[On("resetEvent")]
    public function resetEvent()
    {
        $this->promocodeDiscount = 0;
    }

    private function getTotalSum()
    {
        if ($this->orderType === \App\Models\Product::TYPE_CERTIFICATE) {
            return $this->cart[0]['certificate']['price'];
        }

        $fullPrice = $this->totalWithoutDiscountSum;
        $this->totalDiscount = 0;

        // Если есть промокод
        if ($this->promocode) {
            $promocodeObj = Promocode::where('code', $this->promocode)->first();
            if ($promocodeObj) {
                $discountRate = $promocodeObj->discount / 100;
                $cart = session()->get('cart', []);

                foreach ($cart as $item) {
                    $db_product = Product::find($item['id']);
                    if (!$db_product) continue;

                    // Пропускаем товары со скидкой
                    if ($db_product->discount && $db_product->discount > 0) continue;

                    // Проверяем, применим ли промокод к данному товару
                    if ($promocodeObj->applies_to_all_products ||
                        (is_array($promocodeObj->applicable_products) &&
                         in_array($db_product->id, $promocodeObj->applicable_products))) {

                        $itemTotal = $item['price'] * $item['quantity'];
                        $this->totalDiscount += $itemTotal * $discountRate;
                    }
                }
            }
        }

        $price = $fullPrice - $this->totalDiscount - $this->saleBruler;

        // Применяем бонусные баллы после промокода
        if ($this->bonus) {
            $price = $price - $this->bonus;
        }

        // Применяем сертификат
        if ($this->certificate) {
            $price = $price - $this->certificateRemains;
        }

        // Добавляем стоимость доставки если сумма меньше 15000
        if ($price < 15000) {
            $price = $price + $this->deliveryPrice;
        }

        return $price;
    }

    private function getSaleBruler()
    {
        $sale = 0;

        $products = Product::query()
            ->whereIn("id", \Arr::pluck($this->cart, "id"))
            ->where("discount", ">", 0)
            ->get();
        foreach ($this->cart as $cartItem) {
            // dd($cartItem["id"]);
            $product = $products->firstWhere("id", $cartItem["id"]);
            if ($product) {
                $sale +=
                    $product->price *
                    ($product->discount / 100) *
                    $cartItem["quantity"];
            }
        }
        return $sale;
    }

    // Payments

    // Pay by card
    public function payCard($order, $success_route, $failed_route)
    {
        $order->price = $this->totalOrder;
        $tinkoff = TinkoffController::createPayment(
            $this->totalOrder,
            "Оплата заказа",
            $order->id,
            $success_route,
            $failed_route,
            $order->delivery_price,
            $this->email,
            $this->phone,
            $order->products,
            $this->db_promocode,
            $order
        );
        Log::driver("tinkoff")->error($tinkoff);
        if ($tinkoff["success"] && isset($tinkoff["data"]["PaymentId"])) {
            $order->payment_status = \App\Models\Order::PAYMENT_STATUS_CREATED;
            $order->payment_id = $tinkoff["data"]["PaymentId"];
            $url = $tinkoff["data"]["PaymentURL"];

            $order->status = \App\Models\Order::STATUS_CONFIRMED;
            $order->confirmation_at = now();
            $order->save();
            return redirect($url);
        } else {
            $this->addError(
                "server_error",
                "Ошибка на стороне сервера. Пожалуйста, обратитесь в тех. поддержку"
            );
        }
        $this->isLoading = false;
    }

    public function payYandex($order, $success_route, $failed_route)
    {
        $paymentController = new YandexPayController();
        $response = $paymentController->createOrder(
            $order,
            $success_route,
            $failed_route
        );
        Log::driver("yandex")->error($response);
        if ($response["success"]) {
            $order->update([
                "payment_status" => $paymentController::STATUS_NEW,
                "payment_url" => $response["data"]["data"]["paymentUrl"],
                "confirmation_at" => now(),
            ]);

            CheckYandexPayStatus::dispatch($order);

            return redirect($response["data"]["data"]["paymentUrl"]);
        } else {
            $this->addError(
                "server_error",
                "Ошибка на стороне сервера. Пожалуйста, обратитесь в тех. поддержку"
            );
        }
        $this->isLoading = false;
        return null;
    }

    // Pay by dolyami
    public function payDolyami($order, $success_route, $failed_route)
    {
        $dolyamiController = new DolyamiController();
        $order->price = $this->totalOrder;
        $response = $dolyamiController->createOrder(
            $order,
            $success_route,
            $failed_route
        );
        Log::driver("dolyami")->error($response);
        if ($response && $response["success"]) {
            $order->save();
            $order->update([
                "payment_status" => \App\Models\Order::DOLYAMI_STATUS_NEW,
                "payment_url" => $response["data"]["link"],
                "confirmation_at" => now(),
            ]);
            return redirect($response["data"]["link"]);
        } else {
            $this->addError(
                "server_error",
                "Ошибка на стороне сервера. Пожалуйста, обратитесь в тех. поддержку"
            );
        }
        $this->isLoading = false;
    }

    // Cash (not tested)
    public function payCash($order, $success_route)
    {
        //        $bbController = new BoxberryController();
        //        if ($this->delivery_type == \App\Models\Order::DELIVERY_TYPE_BOXBERRY) {
        //            $result = $bbController->createShipment($this->order->id, $itemsPriceInfo, $this->idPointBB, $customerInfo, $packages, $this->comment);
        //        }

        $order->status = \App\Models\Order::STATUS_PAID_RECEIPT;
        $order->confirmation_at = now();
        $order->save();

        $packages = $this->getPackages($order->products);

        try {
            $this->uuidCdek = "";
            if (
                $order->delivery_type != \App\Models\Order::DELIVERY_TYPE_PICKUP
            ) {
                $this->uuidCdek = CDEKController::afterPayment($order);
            }

            FetchCdekOrderInfo::dispatch(
                $this->uuidCdek,
                $order,
                $packages,
                $this->totalPrice
            )->delay(now()->addMinute());
        } catch (\Exception $e) {
            Log::error($e);
        }
        return redirect($success_route);
    }

    private function getSuccessUrl($order)
    {
        $current_total_price = $this->totalOrder;
        $data = [
            "order_id" => $order->id,
            "type" => $order->type,
            "promocode" => $order->promocode ?? null,
            "order_products" => $order->products,
            "target_email" => $order->target_email ?? $order->recipient_email,
            "certificate" => $this->certificateInCart ?? null,
            "used_certificate" => $this->useCertificate
                ? $this->certificate
                : null,
            "bonus" =>
                $this->useBonus && $this->bonus > 0 ? $this->bonus : null,
            "user_id" => $order->user_id,
            "total_price" => $current_total_price,
        ];
        $success_route = route("success_order", $data);
        return $success_route;
    }

    private function checkAvailability($products)
    {
        foreach ($products as $product) {
            if (isset($product["variant"])) {
                $variant = ProductVariant::find($product["variant"]);
                if ($variant->amount < $product["quantity"]) {
                    $cart = session()->get("cart", []);
                    foreach ($cart as $index => $item) {
                        if ($item["variant"] == $variant->id) {
                            unset($cart[$index]);
                        }
                    }
                    session()->put("cart", $cart);

                    $this->addError(
                        "products_amount",
                        "Извините, товар " . $product["name"] . " был распродан"
                    );
                    $this->isLoading = false;
                    $this->productAmountError = true;

                    return;
                }
            }
        }
    }

    private function saveOrder($order)
    {
        $storeSettings = StoreSetting::first();
        $deliveryType = collect($storeSettings->events["delivery"])
            ->where("id", $this->selectedDeliveryType)
            ->keys()
            ->first();

        $paymentType = collect($storeSettings->events["payments"])
            ->where("id", $this->selectedPaymentType)
            ->keys()
            ->first();

        // Данные о заказе
        $order->type = $this->orderType;
        $order->price_order = $this->totalWithoutDiscountSum;
        $order->price = $this->totalOrder >= 1 ? $this->totalOrder : 0;
        $order->comment = $this->comment;

        // Инфа о пользователе
        $order->user_id = auth()->id() ?? null;
        $order->recipient_name = $this->name;
        $order->recipient_last_name = $this->lastName;
        $order->recipient_email = $this->email;
        $order->recipient_phone = $this->phone;

        // Данные для доставки
        $order->country = $this->country;
        $order->city = $this->city;
        $order->city_code = $this->cityCode;
        $order->delivery_type = $deliveryType;

        if($this->selectedDeliveryType == "cdek") {
            $order->address = $this->addressCdek ?? null;
        }
        if($this->selectedDeliveryType == "cdek_pvz") {
            $order->address = $this->addressPoint ?? null;
        }
        if($this->selectedDeliveryType == "dostavista") {
            $order->address = $this->addressDostavista ?? null;
        }

        $order->delivery_price = $this->totalOrder > 15000 ? 0 : $this->deliveryPrice;
        $order->delivery_info = match ($this->selectedDeliveryType) {
            "dostavista" => $this->dostavistaOrder,
            "pickup" => null,
            default => $order->delivery_info
        };

        // Данные для оплаты
        $order->payment_type = $paymentType;

        // Данные о скидках и бонусах
        $order->promocode = $this->usePromocode ? $this->promocode : null;
        $order->price_with_promocode = $this->usePromocode ? $this->totalOrder : null;
        $order->use_bonus = $this->useBonus;
        $order->points_amount = $this->bonus;

        // Данные о сертификатах
        $order->target_email = $this->target_email;
        $order->certificate = $this->certificate;
        $order->use_certificate = $this->useCertificate;
        $order->cert_amount = $this->certificateRemains;
        $order->save();
        return $order;
    }

    private function getPackages($products)
    {
        $packages = [];
        $counterItems = 1;
        $itemsPriceInfo = [];
        $itemsPriceInfo["delivery_sum"] = $this->deliveryPrice;
        $itemsPriceInfo["price"] = 0;

        if ($this->orderType === \App\Models\Product::TYPE_PRODUCT) {
            foreach ($products as $product) {
                $packages["items"][] = [
                    "id" => (string)$product["id"],
                    "name" => $product["name"],
                    "UnitName" => "шт.",
                    "price" => $product["price"],
                    "quantity" => $product["quantity"],
                ];

                $itemsPriceInfo["price"] += $product["price"];
                $variant = ProductVariant::find($product["variant"]);

                if ($counterItems != 1) {
                    $packages["weights"]["weight" . $counterItems] =
                        $variant["weight"];
                    $packages["weights"]["x" . $counterItems] =
                        $variant["length"];
                    $packages["weights"]["y" . $counterItems] =
                        $variant["height"];
                    $packages["weights"]["z" . $counterItems] =
                        $variant["width"];
                } else {
                    $packages["weights"]["weight"] = $variant["weight"];
                    $packages["weights"]["x"] = $variant["length"];
                    $packages["weights"]["y"] = $variant["height"];
                    $packages["weights"]["z"] = $variant["width"];
                }

                $counterItems++;
            }
        }
        return $packages;
    }

    private function getPriceNotSaleBruler()
    {
        $totalDiscounted = 0;
        $cart = session()->get("cart", []);
        foreach ($cart as $productOrder) {
            $db_product = Product::find($productOrder["id"]);
            if ($db_product->discount == 0) {
                $totalDiscounted +=
                    $db_product->price * $productOrder["quantity"];
            }
        }
        return $totalDiscounted;
    }
}
