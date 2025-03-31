<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\StoreSetting;
use App\Http\Controllers\DolyamiController;

class YandexPayService
{
    protected string $merchantId;
    protected string $apiKey;
    protected bool $testMode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.yandex_pay.merchant_id');
        $this->testMode = config('services.yandex_pay.test_mode', true);
        if ($this->testMode) {
            // TODO: перенести в env
            $this->apiKey = '6d92a6fe-6c06-48e8-9d14-3b080bf649cf';
            $this->baseUrl = 'https://sandbox.pay.yandex.ru/api/merchant/v1/orders';
        } else {
            $this->apiKey = config('services.yandex_pay.api_key');
            $this->baseUrl = 'https://pay.yandex.ru/api/merchant/v1/orders';
        }

    }

    /**
     * Создание заказа и получение ссылки на оплату
     */
    public function createOrder(Order $order, string $successRoute, string $failRoute): array
    {
//        dd('createOrder');
//        dd($order->payment_url);
        if ($order->payment_url) {
            return [
                'status' => 'success',
                'payment_url' => $order->payment_url
            ];
        }

        $payload = [
            'orderId' => (string)$order->id,
            'currencyCode' => 'RUB',
            'availablePaymentMethods' => ['SPLIT'],
            'redirectUrls' => [
                "onSuccess" => $successRoute,
                "onError" => $failRoute
//                'onSuccess' => route('success_order', ['order' => $order->id]),
//                'onError' => route('cart', ['order' => $order->id])
            ],
            'cart' => [
                'total' => ['amount' => (float)$order->price],
                'items' => $this->formatOrderItems($order)
            ]
        ];
//        dd($payload, $this->baseUrl);
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl, $payload);

//        dd($response->json());

        if ($response->successful()) {
            $order->update(['payment_url' => $response->json()['data']['paymentUrl'] ?? null]);
            return [
                'status' => 'success',
                'payment_url' => $response->json()['data']['paymentUrl'] ?? null
            ];
        }

        Log::driver('yandex')->error('Ошибка создания заказа в Yandex Pay: ' . $response->body());
        return ['status' => 'error', 'message' => 'Ошибка платежа'];

    }

    /**
     * Форматирование товаров в заказе
     */

    private function formatOrderItems(Order $order): array
    {
        /*$items = collect($order->products)->map(function ($product) {
            return [
                'productId' => (string) $product['id'],
                'title' => $product['name'],
                'quantity' => ['count' => $product['quantity']],
                'total' => (float) ($product['quantity'] * $product['price'])
            ];
        })->toArray();

        if ($order->delivery_price > 0) {
            $items[] = [
                'productId' => '0',
                'title' => 'Доставка',
                'quantity' => ['count' => 1],
                'total' => (float) $order->delivery_price
            ];
        }

        return $items;*/

        $products_count = 0;
        $productsWithoutSaleAmount = 0;
        $have_free_product = false;
        $storeSettings = StoreSetting::first();
        $freeProductName = '';

        foreach ($order->products as $_product) {
            if ($_product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product'])
                $have_free_product = true;
            $freeProductName = $_product['name'];

            $products_count += $_product['quantity'];

            $discountProduct = Product::find($_product['id'])->discount;
            if (!$discountProduct || $discountProduct == 0) {
                $productsWithoutSaleAmount++;
            }
        }

        $items = [];

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
                if (isset($_product['is_free']) && $_product['is_free']) {
                } else {
                    $productsWithoutSaleAmount++;
                }
            }
        }

        // Проверка на наличие бонусных баллов
        $pointsWriteOff = 0;
        $pointsAmount = $order->points_amount && $order->points_amount > 0 ? $order->points_amount : 0;
        if ($pointsAmount > 0) {
            $pointsWriteOff = round($pointsAmount / $productsWithoutSaleAmount, 2);
        }

        foreach ($order->products as $product) {
            $db_product = Product::find($product['id']);
            $price = $order->type === Product::TYPE_CERTIFICATE
                ? $product['certificate']['price']
                : (isset($db_product->discount)
                    ? $db_product->getDiscountedPrice()
                    : $product['price']);

            $totalPrice = DolyamiController::convertToPrice($price);
            $minusOneItem = false;

            if ((!$db_product->discount || $db_product->discount == 0) && $totalPrice != 0) {
                if (isset($product['is_free']) && $product['is_free'] && $product['quantity'] == 1) {
                } else {
                    if ($promocodePercent) {
                        $totalPrice -= $totalPrice * ($promocodePercent / 100);
                    }

                    if ($pointsWriteOff > 0) {
                        if ($totalPrice > (DolyamiController::convertToPrice($pointsWriteOff) / $product['quantity'])) {
                            $totalPrice -= DolyamiController::convertToPrice($pointsWriteOff) / $product['quantity'];
                            $pointsWriteOff = 0;
                        } else {
                            $minusOneItem = true;
                            $pointsWriteOff -= $totalPrice;
                        }
                    }
                }
            }

            $items[] = [
                'productId' => (string)$product['id'],
                'title' => $product['name'],
                'quantity' => ['count' => (int)$minusOneItem ? $product['quantity'] - 1 : $product['quantity']],
                'total' => (float)$totalPrice,
            ];
        }

        $itemsFinalSum = 0;
        foreach ($items as $item) {
            if ($freeProductName && $freeProductName == $item['title']) {
                $item['quantity']['count'] -= 1;
            }

            $itemsFinalSum += $item['total'] * $item['quantity']['count'];
        }

        if ($order->delivery_price > 0) {
            $items[] = [
                'productId' => '0',
                'title' => 'Доставка',
                'quantity' => ['count' => 1],
                'total' => (float)$order->delivery_price
            ];
        }

        return $items;
    }


    public function chkOrderStatus($orderId)
    {
        return $this->checkOrderStatus($orderId);
    }

    /**
     * Обработка вебхука с проверкой статуса
     */
    public function handleWebhook(array $data): JsonResponse
    {
        Log::info('Yandex Pay Webhook', $data);

        if (!isset($data['event']) || !isset($data['order']['orderId'])) {
            return response()->json(['message' => 'Invalid webhook data'], 400);
        }

        $order = Order::where('external_id', $data['order']['orderId'])->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Проверяем статус заказа
        $OrderStatus = $this->checkOrderStatus($data['order']['orderId']);
        if ($OrderStatus['status'] == 'error') {
            return response()->json(['message' => 'Error in getting order status.'], 400);
        }

        $orderStatusMap = [
            'SUCCESS' => 'paid',
            'FAIL' => 'fail',
            'PENDING' => 'pending',
        ];

        if (!isset($OrderStatus['paymentStatus'])) {

            $updateData = [
                'payment_status' => $OrderStatus['paymentStatus'],
                'payment_id' => $OrderStatus['operationId']
            ];
            if (isset($orderStatusMap[$OrderStatus['paymentStatus']])) {
                $updateData['status'] = $orderStatusMap[$OrderStatus['paymentStatus']];
            }
            $order->update($updateData);

        } else {
            Log::warning('Unknown payment status', ['status' => $OrderStatus['paymentStatus']]);
            return response()->json(['message' => 'Unknown payment status'], 400);
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    /**
     * Проверка статуса заказа
     */
    private function checkOrderStatus(string $orderId): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/{$orderId}");

        if ($response->successful()) {
            $data = $response->json();
            return [
                'status' => 'success',
                'paymentStatus' => $data['data']['order']['paymentStatus'] ?? null,
                'operationId' => $data['data']['operations'][0]['operationId'] ?? null
            ];
        }

        Log::error('Ошибка получения статуса заказа в Yandex Pay: ' . $response->body());
        return ['status' => 'error', 'message' => 'Ошибка проверки'];
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'API-Key ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ];
    }

}
