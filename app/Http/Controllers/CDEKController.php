<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promocode;
use App\Models\StoreSetting;
use CdekSDK2\BaseTypes\Contact;
use CdekSDK2\BaseTypes\Item;
use CdekSDK2\BaseTypes\Location;
use CdekSDK2\BaseTypes\Money;
use CdekSDK2\BaseTypes\Order;
use CdekSDK2\BaseTypes\Package;
use CdekSDK2\BaseTypes\Phone;
use CdekSDK2\BaseTypes\Services;
use CdekSDK2\BaseTypes\Tariff;
use CdekSDK2\BaseTypes\Tarifflist;
use CdekSDK2\Constraints\Currencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CDEKController extends Controller
{
    //
    public $cdek;

    private $url;

    public function __construct()
    {
        $client = new \GuzzleHttp\Client();
        $this->cdek = new \CdekSDK2\Client($client);
        $this->cdek->setAccount(config('services.cdek.account'));
        $this->cdek->setSecure(config('services.cdek.password'));
        $this->url = config('services.cdek.api_test_mode') ? config('services.cdek.api_test_url') : config('services.cdek.api_url');
    }

    public function getCitiesRequest(Request $request)
    {
        $cities = [];
        foreach ($this->getCities($request->query('city'), $request->query('country')) as $city) {
            $cities[] = [
                'label' => $city->city . ', ' . $city->region . ', ' . $city->sub_region,
                'value' => $city->city . ', ' . $city->region . ', ' . $city->sub_region,
                'code' => $city->code,
                'region' => $city->region,
                'sub_region' => $city->sub_region,
            ];
        }

        return $cities;
    }


    public function getCities($city, $country)
    {
        $result = $this->cdek->cities()->getFiltered([
            'country_codes' => $country,
            'city' => $city,
        ]);

        $cities = $this->cdek->formatResponseList($result, \CdekSDK2\Dto\CityList::class);
        return $cities->items;
    }

    public function calculate($code, $address, $country, $packages)
    {
        $tariff = Tarifflist::create([]);
        $tariff->date = (new \DateTime())->format(\DateTime::ISO8601);
        $tariff->type = Tarifflist::TYPE_ECOMMERCE;
        $tariff->currency = Currencies::RUBLE;
        $tariff->lang = Tarifflist::LANG_RUS;

        // Откуда высылаем
        $tariff->from_location = Location::create([
            'code' => config('services.cdek.from.code'),
            'address' => config('services.cdek.from.address'),
            'country_code' => config('services.cdek.from.country_code'),
        ]);

        $tariff->to_location = Location::create([
            'code' => $code,
            'address' => $address,
            'country_code' => $country,
        ]);

        $p = [];
        foreach ($packages as $package) {
            $p[] = Package::create([
                'weight' => $package['weight'],
                'length' => $package['length'],
                'width' => $package['width'],
                'height' => $package['height'],
            ]);
        }
        $tariff->packages = $p;

        $result = $this->cdek->calculator()->add($tariff);
        if ($result->hasErrors()) {
            \Log::driver('cdek')->error(json_encode($result->getErrors()));
            return null;
        }
        if ($result->isOk()) {
            try {
                $tariffs = $this->cdek->formatResponseList($result, \CdekSDK2\Dto\TariffList::class);
            } catch (\Exception $e) {
                // Обработка ошибки
                \Log::channel('cdek')->error(json_encode($e->getMessage()));
            }
            return $tariffs;
        }
    }

    public static function afterPayment(\App\Models\Order $order)
    {
        $order = $order->refresh();
        $self = new self();
        // Если просто доставка, то 137, если ПВЗ, то 136
        $tariff_code = $order->delivery_type == \App\Models\Order::DELIVERY_TYPE_CDEK ? 137 : 136;
        $packages = [];
        $storeSettings = StoreSetting::first();

        $addFreeProduct = false;

        foreach ($order->products as $product) {
            if ($product['type'] == 'set') {
                $variant = ProductVariant::where('product_id', $product['id'])->first()->toArray();
            } else {
                $variant = ProductVariant::find($product['variant']);
            }

            $promocode = Promocode::whereCode($order->promocode)->first();
            $db_product = Product::find($product['id']);
            if (isset($promocode)) {
                $totalAmount = $product['price'] * $product['quantity'];
                $discountedAmount = $totalAmount - ($totalAmount * $promocode->discount / 100);
                $product['price'] = round($discountedAmount);
            }

            $price = $order->type === Product::TYPE_CERTIFICATE
                ? $product['certificate']['price']
                : (isset($db_product->discount) && $db_product->discount != 0
                    ? $db_product->getDiscountedPrice()
                    : $product['price']);

            for ($i = 0; $i < $product['quantity']; $i++) {
                if ($product['quantity'] === 1) {
                    if (isset($product['is_free']) && $product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product'])
                        $price = 0;
                } else {
                    if (isset($product['is_free']) && $product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product']) {
                        $price = !$addFreeProduct ? 0 : $price;
                        $addFreeProduct = true;
                    }
                }
                $packages[] = [
                    'id' => $variant['article'],
                    'name' => $product['name'],
                    'weight' => $variant['weight'],
                    'length' => $variant['length'],
                    'width' => $variant['width'],
                    'height' => $variant['height'],
                    'cost' => $price,
                ];
            }
        }

        $createData = [
            'type' => $order->delivery_type,
            'tariff_code' => $tariff_code,
            'id_order' => $order->id,
            'recipient_name' => $order->recipient_name . ' ' . $order->recipient_last_name,
            'recipient_phone' => $order->recipient_phone,
            'to_location_code' => $order->city_code,
            'to_location_country_code' => $order->country,
            'to_location_address' => $order->address,
            'packages' => $packages,
            'delivery_info' => $order->delivery_info,
            'isCash' => $order->payment_type == \App\Models\Order::PAYMENT_TYPE_CASH,
            'total_price' => $order->price_with_promocode,
            'delivery_price' => $order->delivery_price,
            'comment' => $order->comment
        ];

        return $self->createOrder($createData);
    }

    public function createOrder($orderData)
    {
        $total_weight = 0;
        $items = [];
        foreach ($orderData['packages'] as $package) {
            $total_weight += $package['weight'];
            $items[] = [
                'name' => $package['name'],
                'ware_key' => preg_replace('/[^\p{L}\p{N}\-]/u', '', $package['id']),
                // Предоплата
                'payment' => [
                    'value' => $orderData['isCash'] ? $package['cost'] : 0,
                ],
                'cost' => $package['cost'],
                'weight' => $package['weight'],
                'amount' => 1,
            ];
        }

        $p[] = [
            'number' => 1,
            'weight' => round($total_weight),
            'length' => 40,
            'width' => 5,
            'height' => 47,
            'items' => $items
        ];

        $data = [
            // Интернет-магазин
            'type' => 1,
            'tariff_code' => $orderData['tariff_code'],
            'number' => $orderData['id_order'] . "_" . Str::random(4),
            'recipient' => [
                'name' => $orderData['recipient_name'],
                'phones' => [
                    'number' => $orderData['recipient_phone']
                ]
            ],
            'delivery_recipient_cost' => [
                'value' => $orderData['isCash'] ? $orderData['delivery_price'] : 0,
            ],
            // ПВЗ
            'shipment_point' => 'MSK366',
            'comment' => ($orderData['comment'] ?? ''),
            'packages' => $p,
            'services' => [
                [
                    'code' => 'TRYING_ON'
                ],
                [
                    'code' => 'PART_DELIV'
                ]
            ]
        ];
        // TODO: изменить ID доставок
        if ($orderData['type'] == 2) {
            $data['delivery_point'] = $orderData['delivery_info']['point']['id'];
        } else if ($orderData['type'] == 1) {
            $data['to_location'] = [
                'code' => $orderData['to_location_code'],
                'country_code' => $orderData['to_location_country_code'],
                'address' => $orderData['to_location_address'],
            ];
        }

        $log = 'CDEK data: ' . print_r($data, true);
        file_put_contents(__DIR__ . '/cdek.txt', $log . PHP_EOL, FILE_APPEND);
        $responseOrder = Http::cdek()->post($this->url . '/v2/orders', $data);
        $arrayCdek = $responseOrder->json();
        $status = $responseOrder->status();

        $log = 'CDEK array: ' . print_r($arrayCdek, true);
        file_put_contents(__DIR__ . '/cdek.txt', $log . PHP_EOL, FILE_APPEND);

        $log = 'CDEK status: ' . print_r($status, true);
        file_put_contents(__DIR__ . '/cdek.txt', $log . PHP_EOL, FILE_APPEND);

        if (array_key_exists('entity', $arrayCdek)) {
            $uuid = $arrayCdek['entity']['uuid'];
            return $uuid;
        }
    }
}
