<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Size;
use OwenIt\Auditing\Models\Audit;

class OrderController extends Controller
{
    public function index($id)
    {
        // Получаем заказ по ID или выкидываем ошибку
        $order = Order::findOrFail($id);

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

        // Начинаем основной перебор товаров
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

            $variant = ProductVariant::find($product['variant']);
            if ($variant) {
                $totalPrice = $product['price'] * $product['quantity'];
                if (!$db_product->discount || $db_product->discount == 0) {
                    if ($promocodePercent) {
                        $totalPrice -= $totalPrice * ($promocodePercent / 100);
                    }

                    if ($pointsWriteOff > 0) {
                        $totalPrice -= $pointsWriteOff;
                    }
                }

                $productsInOrder[] = [
                    'Номенклатура' => [
                        'Наименование' => $product['name'],
                        'Артикул' => $variant->article,
                        'Размер' => $this->getSizeName($variant->size_id),
                        'Услуга' => false
                    ],
                    'Количество' => $product['quantity'],
                    'Цена' => $product['price'],
                    'Сумма' => round($totalPrice, 2),
                    'СтавкаНДС' => 'Без НДС',
                    'СуммаНДС' => 0,
                    'Бесплатно' => $product['is_free'] ? true : false
                ];
            }
        }

        if (!$order->use_certificate) {
            $itemsFinalSum = 0;
            foreach ($productsInOrder as $item) {
                $itemsFinalSum += $item['Сумма'];
            }

            if ($order->price != $itemsFinalSum) {
                $differencePrice = $order->price - $itemsFinalSum;
                $productsInOrder[array_key_last($productsInOrder)]['Сумма'] += $differencePrice;
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

        if ($order->price_with_promocode) {
            $customerPaymentAmount = (float) $order->price_with_promocode + $deliveryPrice;
        }

        $arrayOrder = [
            [
                "ЮрЛицо" => true,
                "ОфициальноеНаименование" => "ООО «БРУЛЕР»",
                "ИНН" => "9714017207",
                "КПП" => "771401001",
                "Заказы" => [
                    [
                        "Номер" => $order->id,
                        'Дата' => $order->created_at->format('Y-m-d H:i:s'),
                        "СпособДоставки" => $this->getDeliveryType($order),
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
                    ]
                ],
            ]
        ];

        if ($order->delivery_type !== Order::DELIVERY_TYPE_PICKUP && $deliveryPrice > 0) {
            $arrayOrder[0]['Заказы'][0]['Товары'][] = [
                'Номенклатура' => [
                    'Наименование' => 'Доставка',
                    'Услуга' => true
                ],
                'Количество' => 1,
                'Цена' => $deliveryPrice,
                'Сумма' => $deliveryPrice,
                'СтавкаНДС' => 'Без НДС',
                'СуммаНДС' => 0,
                'Бесплатно' => false
            ];
        }

        $arrayOrder[0]['Заказы'][0] = array_filter($arrayOrder[0]['Заказы'][0], function ($value) {
            return !is_null($value);
        });

        return $arrayOrder;
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
    private function getDeliveryType($order)
    {
        return $order->delivery_type > 1 ? 'Самовывоз' : 'Доставка';
    }

    private function getDeliveryPrice($order)
    {
        return $order->delivery_price ?? ($order->delivery_info['delivery']['delivery_sum'] ?? 0);
    }

    private function getAddress($order)
    {
        if ($order->delivery_type === Order::DELIVERY_TYPE_CDEK) {
            return 'г.' . $order['city'] . ', ' . $order['address'];
        }

        if ($order->delivery_type === Order::DELIVERY_TYPE_CDEK_PVZ) {

            $point = $order->delivery_info['point'] ?? null;
            if ($point) {
                return $point['city'] . ', ' . $point['address'];
            }
            return '-';
        }
        return null;
    }

    private function getManager($order)
    {
        $audit = Audit::query()
            ->where('auditable_id', $order->id)
            ->where('auditable_type', 'App\Models\Order')
            ->whereJsonContains('new_values', ['status' => 'confirmed'])
            ->first();

        if ($audit) {
            return $audit->user->name . ' ' . $audit->user->last_name;
        }
        return null;
    }

    private function getSizeName($size_id)
    {
        $size = Size::query()->where('id', $size_id)->first();
        return $size ? $size->name : null;
    }
}
