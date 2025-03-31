<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promocode;
use App\Models\Size;
use App\Models\StoreSetting;
use Cache;
use Carbon\Carbon;
use OwenIt\Auditing\Models\Audit;

class OrdersController extends Controller
{

    private $variants;
    private $status;

    public function __invoke()
    {
        $this->status = Order::STATUS_SHIPPING;
        if (request('token') == env('API_TOKEN')) {
            $result = [];

            $this->variants = ProductVariant::all();

            $orders = Order::query()
                ->where('status', $this->status)
                ->whereDate('created_at', '>=', Carbon::now()->subDays(3))
                ->get();

            $ids = $orders->pluck('id')->toArray();
            $orderIdsNotInCache = $this->checkNewOrderIds($ids);
            $orders = $orders->whereIn('id', $orderIdsNotInCache);
            if ($orders->count() == 0) {
                return response()->json();
            }
            foreach ($orders as $order) {
                $result[] = $this->getOrder($order);
            }
            $result = [
                [
                    "ЮрЛицо" => true,
                    "ОфициальноеНаименование" => "ООО «БРУЛЕР»",
                    "ИНН" => "9714017207",
                    "КПП" => "770101001",
                    "Заказы" => $result,
                ]
            ];

            return response()->json($result);
        }
        return response()->json(['error' => 'Invalid token'], 401);
    }

    private function getOrder($order): array
    {
        // Проверка на примененый промокод
        $promocodePercent = null;
        if ($order->promocode) {
            $promocodeOrder = Promocode::where('code', $order->promocode)->first();
            if ($promocodeOrder) {
                $promocodePercent = $promocodeOrder->discount;
            }
        }

        // Сумма товаров на которые должны быть применены акции
        $productsWithoutSaleAmount = 0;

        // Получаем сумму товаров на которые должны быть применены акции
        foreach ($order->products as $_product) {
            $discountProduct = Product::find($_product['id']);
            if ($discountProduct && (!$discountProduct->discount || $discountProduct->discount == 0)) {
                $productsWithoutSaleAmount++;
            }
        }

        // Проверка на наличие бонусных баллов
        $pointsWriteOff = 0;
        $pointsAmount = $order->points_amount && $order->points_amount > 0 ? $order->points_amount : 0;
        if ($pointsAmount > 0) {
            $pointsWriteOff = round($pointsAmount / $productsWithoutSaleAmount, 2);
        }

        // Итоговый массив товаров
        $productsInOrder = [];

        foreach ($order->products as $product) {
            $db_product = Product::find($product['id']);
            if (!$db_product) {
                continue;
            }

            if (!isset($product['variant'])) {
                if ($product['type'] == 'certificate') {
                    $productsInOrder[] = [
                        'Номенклатура' => [
                            'Наименование' => $order->certificate['name'] . ' №' . $order->certificate['id'],
                            'Артикул' => '00001',
                            'Услуга' => false
                        ],
                        'Количество' => 1,
                        'Цена' => $product['quantity'],
                        'Сумма' => $product['price'] * $product['quantity'],
                        'СтавкаНДС' => 'Без НДС',
                        'СуммаНДС' => 0
                    ];
                }
                continue;
            }

            // if ($product['type'] == 'set') {
            //     $set_ids = \Arr::pluck(\Arr::flatten($product['set_products'], 1), 'id');
            //     $set_products = Product::query()
            //         ->whereIn('id', $set_ids)
            //         ->get();
            //     foreach ($set_products as $set_product) {
            //         $variant = $this->variants->where('product_id', $set_product['id'])->first();
            //         $price = round($set_product['price'] - ($set_product['price'] * $percent), 2);
            //         $productsInOrder[] = [
            //             'Номенклатура' => [
            //                 'Наименование' => $set_product['name'],
            //                 'Артикул' => $variant->article,
            //                 'Размер' => $this->getSizeName($variant->size_id),
            //                 'Услуга' => false
            //             ],
            //             'Количество' => 1,
            //             'Цена' => $price,
            //             'Сумма' => $price * 1,
            //             'СтавкаНДС' => 'Без НДС',
            //             'СуммаНДС' => 0, // $product['price'] * $product['quantity'] * 0.2
            //             "Бесплатно" => $set_product['is_free'] ? true : false,
            //             "Сет" => true
            //         ];
            //     }
            // }

            $variant = ProductVariant::find($product['variant']);
            if ($variant) {
                $productsInOrder[] = [
                    'Номенклатура' => [
                        'Наименование' => $product['name'],
                        'Артикул' => $variant->article,
                        'Размер' => $this->getSizeName($variant->size_id),
                        'Услуга' => false
                    ],
                    'Количество' => $product['quantity'],
                    'Цена' => $product['price'],
                    'Сумма' => $product['price'] * $product['quantity'],
                    'СтавкаНДС' => 'Без НДС',
                    'СуммаНДС' => 0,
                    'Бесплатно' => $product['is_free'] ? true : false
                ];
            }
        }

        $deliveryPrice = $this->getDeliveryPrice($order);

        $customerPaymentAmount = (float) $order->price_order + $deliveryPrice;

        if ($order->cert_amount > 0) {
            $customerPaymentAmount -= (float) $order->cert_amount;
        }

        if ($pointsAmount > 0) {
            $customerPaymentAmount -= (float) $pointsAmount;
        }

        if ($order->promocode) {
            $customerPaymentAmount = (float) $order->price_with_promocode + $deliveryPrice;
        }

        $storeSettings = StoreSetting::first();
//        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"],fn($payment,$key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"],fn($delivery,$key) => [$key => $delivery["label"]]);



        $arrayOrder = [
            "Номер" => $order->id,
            'Дата' => $order->created_at->format('Y-m-d H:i:s'),
            "СпособДоставки" => $deliveryOptions[$order->delivery_type] ?? 'Доставка',
            "АдресДоставки" => $this->getAddress($order),
            "Клиент" => [
                "Фамилия" => $order->recipient_name,
                "Имя" => $order->recipient_last_name,
                "Телефон" => $order->recipient_phone,
                "Email" => $order->recipient_email ?? '',
            ],
            'Менеджер' => $this->getManager($order) ?? 'admin',
            'СуммаДокумента' => (float) $order->price_order + $deliveryPrice,
            'СуммаВключаетНДС' => 0,
            "Промокод" => $order->promocode,
            'СуммаОплатыКлиента' => $customerPaymentAmount > 0 ? $customerPaymentAmount : 0,
            'СуммаПромокодаВключаетНДС' => $order->price_with_promocode ? 0 : null,
            'ПримененПодарочныйСертификат' => $this->useCertificate($order),
            'Товары' => count($productsInOrder) > 0 ? $productsInOrder : null
        ];

        if ($order->delivery_type !== Order::DELIVERY_TYPE_PICKUP && $deliveryPrice > 0) {
            $arrayOrder['Товары'][] = [
                'Номенклатура' => [
                    'Наименование' => 'Доставка',
                    'Услуга' => true
                ],
                'Количество' => 1,
                'Цена' => $deliveryPrice,
                'Сумма' => $deliveryPrice,
                'СтавкаНДС' => 'Без НДС',
                'СуммаНДС' => 0,
                "Бесплатно" => false
            ];
        }

        return array_filter($arrayOrder, function ($value) {
            return !is_null($value);
        });
    }

    private function getNDS(array $productsInOrder)
    {
        return collect($productsInOrder)->where('СтавкаНДС', 'НДС20')->sum('СуммаНДС');
    }

    private function useCertificate($order)
    {
        if ($order->use_certificate) {
            $certSum = $order->cert_amount;
            if ($order->cert_amount > $order->price_order) {
                $certSum = $order->price_order;
            }

            $certificate = [
                "Номер" => "АБ00001",
                "Сумма" => $certSum,
            ];
            return $certificate;
        }
        return null;
    }

    private function getDeliveryPrice($order)
    {
        return $order->delivery_price ?? ($order->delivery_info['delivery']['delivery_sum'] ?? 0);
    }

    private function getAddress($order)
    {
        if ($order->delivery_type === Order::DELIVERY_TYPE_CDEK) {
            return $order['city'] . ', ' . $order['address'];
        }

        if ($order->delivery_type === Order::DELIVERY_TYPE_CDEK_PVZ) {
            $point = $order->delivery_info['point'] ?? null;
            return $point ? $point['city'] . ', ' . $point['address'] : '';
        }
        return null;
    }

    private function getManager($order)
    {
        $audit = Audit::query()
            ->where('auditable_id', $order->id)
            ->where('auditable_type', 'App\Models\Order')
            ->whereJsonContains('new_values', ['status' => $this->status])
            ->first();
        if ($audit) {
            $user = $audit->user;
            return $user ? $audit->user->name . ' ' . $audit->user->last_name : 'anonymous';
        }
        return null;
    }

    public function checkNewOrderIds($newOrderIds)
    {
        $cachedOrderIds = Cache::get('cached_order_ids', []);
        $orderIdsNotInCache = array_diff($newOrderIds, $cachedOrderIds);
        Cache::put('cached_order_ids', $newOrderIds, now()->addDay());
        return $orderIdsNotInCache;
    }

    private function getSizeName($size_id)
    {
        $size = Size::query()->where('id', $size_id)->first();
        return $size ? $size->name : null;
    }
}
