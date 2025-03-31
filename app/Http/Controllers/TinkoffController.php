<?php

namespace App\Http\Controllers;

use App\Jobs\FetchCdekOrderInfo;
use Illuminate\Support\Facades\Bus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\StoreSetting;
use App\Models\User;
use App\Notifications\OrderConfirmed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class TinkoffController extends Controller
{
    private static $api_url = 'https://securepay.tinkoff.ru/v2/';
    private $terminalKey;
    private $secretKey;

    public static function getInstance(string $terminal = 'default')
    {
        Log::info('Init Terminal: ' . $terminal);
        $instance = new self();
        $instance->initClient(config("services.tinkoff_{$terminal}.terminal"), config("services.tinkoff_{$terminal}.secret"));
        return $instance;
    }

    /**
     * Конвертирование в копейки
     */
    public static function convertFromRUB(float $amount)
    {
        return round($amount * 100);
    }

    public static function createPayment(
        float $amount,
        string $description,
        string $paymentId,
        string $success_route,
        string $failed_route,
        $delivery_price,
        $email,
        $phone,
        $products,
        $promocode,
        $order
    )
    {
        $data = [
            'Order_number' => $paymentId,
        ];
        $products_count = 0;
        $productsWithoutSaleAmount = 0;
        $have_free_product = false;
        $storeSettings = StoreSetting::first();

        foreach ($order->products as $_product) {
            if ($_product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product'])
                $have_free_product = true;
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
                if (isset($_product['is_free']) && $_product['is_free'] && $_product['quantity'] == 1) {
                } else {
                    $productsWithoutSaleAmount++;
                }
            }
        }

        // Проверка на наличие бонусных баллов
        $pointsWriteOff = 0;
        $pointsAmount = $order->points_amount && $order->points_amount > 0 ? $order->points_amount : 0;
        if ($pointsAmount > 0 && $productsWithoutSaleAmount > 0) {
//            $pointsWriteOff = round($pointsAmount / $productsWithoutSaleAmount, 2);
            $pointsWriteOff = $pointsAmount;
        }

        foreach ($products as $product) {
            $db_product = Product::find($product['id']);
            $price = $order->type === Product::TYPE_CERTIFICATE
                ? $product['certificate']['price']
                : (isset($db_product->discount)
                    ? $db_product->getDiscountedPrice()
                    : $product['price']);

            if ($order->use_certificate && $have_free_product) {
                $price = $order->price / ($products_count - 1);
            }

            $totalPrice = static::convertFromRUB($price * $product['quantity']);

            if (isset($product['is_free']) && $product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product']) {
                if ($product['quantity'] === 1) {
                    $totalPrice = 0;
                } else {
                    $totalPrice = static::convertFromRUB($price * ($product['quantity'] - 1));
                }
            }

            if ((!$db_product->discount || $db_product->discount == 0) && $totalPrice != 0) {
                if ($pointsWriteOff > 0) {
                    $totalPrice -= static::convertFromRUB($pointsWriteOff);
                }

                if ($promocodePercent) {
                    $totalPrice -= $totalPrice * ($promocodePercent / 100);
                }
            }

            $items[] = [
                'Name' => $product['name'],
//                'Price' => $totalPrice / $product['quantity'], // TODO: разобраться
//                'Price' => ($totalPrice == 0) ? 0 : static::convertFromRUB($price),
                'Price' => $totalPrice,
                'Quantity' => $product['quantity'],
                'Amount' => $totalPrice,
                'Tax' => 'none',
                'PaymentMethod' => 'full_prepayment',
                'PaymentObject' => 'commodity',
                "MeasurementUnit" => "шт."
            ];
        }

        if ($delivery_price > 0) {
            $items[] = [
                'Name' => 'Доставка',
                'Price' => static::convertFromRUB($order->delivery_price),
                'Quantity' => 1,
                'Amount' => static::convertFromRUB($order->delivery_price),
                'Tax' => 'none',
                'PaymentMethod' => 'full_prepayment',
                'PaymentObject' => 'service',
                "MeasurementUnit" => "ед."
            ];
        }

        $itemsFinalSum = 0;
        foreach ($items as $item) {
            $itemsFinalSum += $item['Amount'];
        }

        if ($order->use_certificate) {
            $distributionCertSum = static::convertFromRUB($order->cert_amount);

            if ($distributionCertSum > $itemsFinalSum) {
                $distributionCertSum = $itemsFinalSum;
            }

            $itemsFinalSum = 0;

            foreach ($items as $ind => $item) {
                if ($distributionCertSum > 0) {
                    if ($items[$ind]['Amount'] >= $distributionCertSum) {
                        $items[$ind]['Amount'] -= $distributionCertSum;
                        $distributionCertSum = 0;
                    } else {
                        $distributionCertSum -= $items[$ind]['Amount'];
                        $items[$ind]['Amount'] = 0;
                    }
                }
                $items[$ind]['Price'] = $items[$ind]['Amount'] / $items[$ind]['Quantity'];
                $itemsFinalSum += $items[$ind]['Amount'];
            }
        }
        if (static::convertFromRUB($order->price) != $itemsFinalSum) {
            $differencePrice = static::convertFromRUB($order->price) - $itemsFinalSum;
            $items[array_key_last($items)]['Amount'] += $differencePrice;
        }

        $params = [
            'Amount' => static::convertFromRUB($amount),
            'OrderId' => $paymentId,
            'Description' => $description,
            'SuccessURL' => $success_route,
            'FailURL' => $failed_route,
            'NotificationURL' => route('tinkoff.webhook'),
            'PayType' => 'O',
            // 'RedirectDueDate' => Carbon::now()->addMinutes(5)->format(DATE_RFC3339),
            'DATA' => $data,
            'Receipt' => [
                'Email' => $email,
                'Phone' => $phone,
                'Taxation' => 'usn_income',
                'Items' => $items,
                'FfdVersion' => '1.2'
            ]
        ];

        $instance = static::getInstance();
        Log::channel('payments')->info(json_encode($params, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $result = $instance->init($params);

        if ($result['success']) {
            return $result;
            $payload['PaymentId'] = $result['data']['PaymentId'];
            $url = $result['data']['PaymentURL'];
            return $url;
        }
        return '';
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        Log::warning(json_encode($request->all()));
        if (!isset($request->OrderId))
            return new Response('', 423);

        $order = Order::where('id', $request->OrderId)->firstOrFail();

        $instance = static::getInstance();
        $token = $instance->_genToken($request->all());
        if ($token != $request->Token) {
            Log::error("Token not valid. instance: $instance->terminalKey Token: {$token} != {$request->Token}");
            return new Response('', 422);
        }

        // Проверяем статус заказа
        if ($order->payment_status == Order::PAYMENT_STATUS_PAID) {
            Log::info("Order {$order->id} already processed.");
            return new Response('OK', 200);
        }

        switch ($request->Status) {
            case "AUTHORIZED":
                Log::info("Order {$order->id} is authorized.");
                $instance->confirm([
                    'PaymentId' => $request->PaymentId,
                    'Amount' => $request->Amount
                ]);
                break;
            case "CONFIRMED":
                Log::info("Order {$order->id} is confirmed.");
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                $order->paid_at = now();
                $order->status = Order::STATUS_PAID;
                $order->save();

                $user = new User();
                $user->email = $order->recipient_email;
                $user->notify(new OrderConfirmed($order->recipient_name, $order));

                if ($order->type !== Product::TYPE_CERTIFICATE) {
                    foreach ($order->products as $product) {
                        $packagesCdek['items'][] = [
                            "id" => (string) $product['id'],
                            "name" => $product['name'],
                            "UnitName" => "шт.",
                            "price" => $product['price'],
                            "quantity" => $product['quantity']
                        ];
                    }

                    // TODO: заменить ID
                    $uuidCdek = '';
                    if ($order->delivery_type != 3 && $order->delivery_type != 0) {
                        $uuidCdek = CDEKController::afterPayment($order);
                    }

                    \App\Jobs\FetchOrderInfo::dispatch($order, $uuidCdek);

//                    FetchCdekOrderInfo::dispatch($uuidCdek, $order, $packagesCdek, $order->price)
//                        ->delay(now()->addMinute());
                }

                break;
            case "REJECTED":
                Log::info("Order {$order->id} is rejected.");
                $order->payment_status = Order::PAYMENT_STATUS_REJECTED;
                $order->save();
                break;
            default:
                Log::error("Order {$order->id} has unknown status: {$request->Status}");
                return new Response('', 422);
        }

        return new Response('OK', 200);
    }



    public function initClient($terminalKey, $secretKey)
    {
        $this->terminalKey = $terminalKey;
        $this->secretKey = $secretKey;
    }

    /**
     * @param $args array You could use associative array or url params string
     * @return array
     */
    public function init(array $args)
    {
        return $this->buildQuery('Init', $args);
    }

    public function getQr(array $args)
    {
        return $this->buildQuery('GetQr', $args);
    }


    public function getState($args)
    {
        return $this->buildQuery('GetState', $args);
    }

    public function confirm(array $args)
    {
        Log::channel('payments')->info(json_encode($args));
        return $this->buildQuery('Confirm', $args);
    }

    public function cancel(array $args)
    {
        return $this->buildQuery('Cancel', $args);
    }

    public function charge($args)
    {
        return $this->buildQuery('Charge', $args);
    }

    public function addCustomer($args)
    {
        return $this->buildQuery('AddCustomer', $args);
    }

    public function getCustomer($args)
    {
        return $this->buildQuery('GetCustomer', $args);
    }

    public function removeCustomer($args)
    {
        return $this->buildQuery('RemoveCustomer', $args);
    }

    public function getCardList($args)
    {
        return $this->buildQuery('GetCardList', $args);
    }

    public function removeCard($args)
    {
        return $this->buildQuery('RemoveCard', $args);
    }

    /**
     * Builds a query string and call sendRequest method.
     * Could be used to custom API call method.
     *
     * @param string $path API method name
     * @param mixed $args query params
     *
     * @return mixed
     */
    public function buildQuery($path, $args)
    {
        $url = static::$api_url;
        if (is_array($args)) {
            if (!array_key_exists('TerminalKey', $args)) {
                $args['TerminalKey'] = $this->terminalKey;
            }
            if (!array_key_exists('Token', $args)) {
                $args['Token'] = $this->_genToken($args);
            }
        }
        $url = $this->_combineUrl($url, $path);


        return $this->_sendRequest($url, $args);
    }

    /**
     * Generates Token
     *
     * @param $args
     * @return string
     */
    public function _genToken($args)
    {
        $token = '';
        $args['Password'] = $this->secretKey;
        if (isset($args['Token'])) {
            Arr::forget($args, 'Token');
        }
        ksort($args);

        foreach ($args as $arg) {
            if (!is_array($arg)) {
                if (is_bool($arg))
                    $arg = $arg ? 'true' : 'false';
                $token .= (string) $arg;
            }
        }
        $token = hash('sha256', $token);

        return $token;
    }

    /**
     * Combines parts of URL. Simply gets all parameters and puts '/' between
     *
     * @return string
     */
    private function _combineUrl()
    {
        $args = func_get_args();
        $url = '';
        foreach ($args as $arg) {
            if (is_string($arg)) {
                if ($arg[strlen($arg) - 1] !== '/')
                    $arg .= '/';
                $url .= $arg;
            } else {
                continue;
            }
        }

        return $url;
    }

    /**
     * Main method. Call API with params
     *
     * @param $api_url
     * @param $args
     * @return array
     *
     */
    private function _sendRequest(string $api_url, array $args)
    {

        $response = Http::withHeaders([
//        $response = Http::withoutVerifying([
            'Content-Type' => 'application/json'
        ])
            ->post($api_url, $args);

        if ($response->successful()) {
            $json = $response->json();
            Log::info(json_encode($json));
            return [
                'success' => true,
                'data' => $json
            ];
        } else {
            return [
                'success' => false,
                'data' => []
            ];
        }
    }
}
