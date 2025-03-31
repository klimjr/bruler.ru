<?php

namespace App\Http\Controllers;

use App\Jobs\FetchCdekOrderInfo;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\StoreSetting;
use App\Services\YandexService;
use Log;

/**
 * Класс контроллер для взаимодействия с оплатой yandex pay
 */
class YandexPayController extends Controller
{

    /**
     * Набор констант статусов, используемых в yandex
     */
    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'PENDING';
    const STATUS_AUTHORIZED = 'AUTHORIZED';
    const STATUS_CAPTURED = 'CAPTURED';
    const STATUS_VOIDED = 'VOIDED';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_REFUNDED = 'REFUNDED';
    const STATUS_PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    const STATUS_FAILED = 'FAILED';

    /**
     * Сервис для взаимодействия с yandexPay
     *
     * @var YandexService
     */
    protected YandexService $service;

    /**
     *
     */
    function __construct()
    {
        $this->service = YandexService::getInstance();
    }

    /**
     * Создать заказ
     *
     * Метод соберет данные для отправки заказ в yandex и отправит запрос на создание заказа
     *
     * @param Order $order
     * @param string $successRoute
     * @param string $failRoute
     * @return array
     */
    public function createOrder(Order $order, string $successRoute, string $failRoute): array
    {
        $items = $this->getOrderItems($order);

        $itemsFinalSum = 0;
        foreach ($items as $item) {
            $itemsFinalSum += $item['TOTAL_PRICE'];
        }

        if ($order->use_certificate) {
            $distributionCertSum = numberToPrice($order->cert_amount);

            if ($distributionCertSum > $itemsFinalSum) {
                $distributionCertSum = $itemsFinalSum;
            }

            $itemsFinalSum = 0;

            foreach ($items as $ind => $item) {
                if ($distributionCertSum > 0) {
                    if ($items[$ind]['TOTAL_PRICE'] >= $distributionCertSum) {
                        $items[$ind]['TOTAL_PRICE'] -= $distributionCertSum;
                        $distributionCertSum = 0;
                    } else {
                        $distributionCertSum -= $items[$ind]['TOTAL_PRICE'];
                        $items[$ind]['TOTAL_PRICE'] = 0;
                    }
                }
                $itemsFinalSum += $items[$ind]['TOTAL_PRICE'];
            }
        }


//        if (numberToPrice($order->price) != $itemsFinalSum && !empty($order->price)) {//
//            $differencePrice = numberToPrice($order->price) - $itemsFinalSum;
//            $items[array_key_last($items)]['TOTAL_PRICE'] += $differencePrice;
//        }

        if ($order->price != $itemsFinalSum && !empty($order->price)) {
            $differencePrice = $order->price - $itemsFinalSum;
            $items[array_key_first($items)]['TOTAL_PRICE'] += $differencePrice;
        }

        $products = [];
        foreach ($items as $item) {
            $products[] = [
                'productId' => (string)$item['ID'],
                'title' => $item['NAME'],
                'quantity' => [
                    'count' => $item['QUANTITY'],
                ],
                'total' => numberToPrice($item['TOTAL_PRICE'])
            ];
        }


        $params = [
            'orderId' => (string)($order->id),
            'cart' => [
                'items' => $products,
                'total' => [
//                    'amount' => numberToPrice(!empty($order->price) ? $order->price : $itemsFinalSum),
                    'amount' => (string)numberToPrice(!empty($order->price) ? $order->price : $itemsFinalSum),
                ]
            ],
            'currencyCode' => 'RUB',
            'merchantId' => config('services.yandex_pay.merchant_id'),
            'redirectUrls' => [
                "onSuccess" => $successRoute,
                "onError" => $failRoute
            ],
            'availablePaymentMethods' => ["CARD", "SPLIT"],
            'ttl' => 1800

        ];
        return $this->service->createOrder($params);
    }

    /**
     * Получить элементы заказ
     *
     * Метод собирает товары и прочие платные элементы из заказа, для последующей обработки и передачи при создании заказа
     *
     * @param Order $order
     * @return array
     */
    public function getOrderItems(Order $order): array
    {
        $result = [];
        $productsWithoutSaleAmount = 0;
        $storeSettings = StoreSetting::first();

        $promocodePercent = null;
        $promocodeData = $this->getPromocodeData($order);
        if (!empty($promocodeData)) {
            $promocodePercent = $promocodeData->discount;
        }

        foreach ($order->products as $product) {
            $productInfo = $this->getProductInOrderInfo($product, $order, $storeSettings);
            if ($productInfo['WITHOUT_SALE']) {
                $productsWithoutSaleAmount++;
            }
            $result[$product['id']] = $productInfo;
        }

        // Проверка на наличие бонусных баллов
        $pointsWriteOff = 0;
        $pointsAmount = $order->points_amount && $order->points_amount > 0 ? $order->points_amount : 0;

        if ($pointsAmount > 0 && $productsWithoutSaleAmount > 0) {
            $pointsWriteOff = round($pointsAmount / $productsWithoutSaleAmount, 2);
        }

        foreach ($order->products as $product) {
            $productData = $result[$product['id']];
            $totalPrice = $productData['PRICE'];
            $minusOneItem = false;

            if ($productData['WITHOUT_SALE'] && $productData['PRICE'] != 0 && $product['quantity'] != 1) {
                if ($promocodePercent) {
                    $totalPrice -= $totalPrice * ($promocodePercent / 100);
                }
                if ($pointsWriteOff > 0) {
                    if ($totalPrice > (numberToPrice($pointsWriteOff) / $product['quantity'])) {
                        $totalPrice -= numberToPrice($pointsWriteOff) / $product['quantity'];
                        $pointsWriteOff = 0;
                    } else {
                        $minusOneItem = true;
                        $pointsWriteOff -= $totalPrice;
                    }
                }
            }
            $result[$product['id']]['CURRENT_PRICE'] = $totalPrice;
            $result[$product['id']]['QUANTITY'] = (int)$minusOneItem ? $product['quantity'] - 1 : $product['quantity'];
            $result[$product['id']]['TOTAL_PRICE'] = $result[$product['id']]['QUANTITY'] * (!empty($result[$product['id']]['CURRENT_PRICE']) ? $result[$product['id']]['CURRENT_PRICE'] : $result[$product['id']]['PRICE']);
        }

        $deliveryItem = $this->getOrderDeliveryItem($order);
        if (!empty($deliveryItem)) {
            $result[] = $deliveryItem;
        }

        return array_values($result);
    }

    /**
     * Получить информацию по продукту в заказе
     *
     * Собирает данные для конкретного продукта в заказе
     *
     * @param array $orderProductData
     * @param Order $order
     * @param StoreSetting $storeSettings
     * @return array
     */
    protected function getProductInOrderInfo(array $orderProductData, Order $order, StoreSetting $storeSettings): array
    {
        $result = [
            'IS_FREE' => false,
            'QUANTITY' => $orderProductData['quantity'],
            'NAME' => $orderProductData['name'],
            'PRICE' => 0,
            'TYPE' => 'PRODUCT',
            'WITHOUT_SALE' => false,
            'ID' => $orderProductData['id']
        ];

        if (!empty($orderProductData['is_free']) && isset($storeSettings) && $storeSettings->events['use_free_three_product']) {
            $result['IS_FREE'] = true;
        }

        $productItem = Product::find($orderProductData['id']);
        if ($productItem && (!$productItem->discount || $productItem->discount == 0)) {
            if (!(isset($orderProductData['is_free']) && $orderProductData['is_free'])) {
                $result['WITHOUT_SALE'] = true;
            }
        }

        $price = $order->type === Product::TYPE_CERTIFICATE
            ? $orderProductData['certificate']['price']
            : (isset($productItem->discount)
                ? $productItem->getDiscountedPrice()
                : $orderProductData['price']);

        $result['PRICE'] = numberToPrice($price);
        return $result;
    }

    /**
     * Получить промокод заказа
     *
     * @param Order $order
     * @return Promocode|null
     */
    public function getPromocodeData(Order $order): Promocode|null
    {
        if ($order->promocode) {
            $promocodeOrder = Promocode::where('code', $order->promocode)->first();
            if ($promocodeOrder) {
                return $promocodeOrder;
            }
        }
        return null;
    }

    /**
     * Получить информацию о доставке
     *
     * Используется в общих данных для передачи заказа в yandex
     *
     * @param Order $order
     * @return array
     */
    public function getOrderDeliveryItem(Order $order): array
    {
        $result = [];
        if ($order->delivery_price > 0) {
            $result = [
                'IS_FREE' => false,
                'QUANTITY' => 1,
                'NAME' => 'Доставка',
                'ID' => 'delivery',
                'PRICE' => numberToPrice($order->delivery_price),
                'TOTAL_PRICE' => numberToPrice($order->delivery_price),
                'WITHOUT_SALE' => false,
            ];
        }
        return $result;
    }

    /**
     * Проверить статус
     *
     * Проверит статус заказа в yandex
     *
     * @param Order $order
     * @return void
     */
    public function checkStatus(Order $order)
    {
        $data = $this->service->getOrder((string)$order->id);
        if ($data['success']) {
            $paymentStatus = $data['data']['data']['order']['paymentStatus'];
            switch ($paymentStatus) {
                case self::STATUS_CONFIRMED:
                    $order->update(
                        ['payment_status' => self::STATUS_CONFIRMED, 'paid_at' => now(), 'status' => Order::STATUS_PAID]
                    );
                    foreach ($order->products as $product) {
                        $packagesCdek['items'][] = [
                            "id" => (string)$product['id'],
                            "name" => $product['name'],
                            "UnitName" => "шт.",
                            "price" => $product['price'],
                            "quantity" => $product['quantity']
                        ];
                    }
                    $uuidCdek = '';
                    if ($order->delivery_type != 3 && $order->delivery_type != 0) {
                        $uuidCdek = CDEKController::afterPayment($order);
                    }
                    FetchCdekOrderInfo::dispatch($uuidCdek, $order, $packagesCdek, $order->price)
                        ->delay(now()->addMinute());
                    break;
                default:
                    $order->update(['payment_status' => $paymentStatus]);
                    break;
            }
        } else {
            Log::error("Order {$order->id} not found yandex pay status");
        }
    }
}

