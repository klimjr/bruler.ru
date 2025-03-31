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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DolyamiController extends Controller
{
    private static string $api_url = 'https://partner.dolyame.ru/v1/';
    private string $path_sert;
    private string $path_key;
    private string $login;
    private string $password;

    private bool $isProduction;

    function __construct(public $debug = false)
    {
        $this->path_sert = config('services.dolyami.cert');
        $this->path_key = config('services.dolyami.private_key');
        $this->login = config('services.dolyami.login');
        $this->password = config('services.dolyami.password');
        $this->isProduction = true;
    }

    function generateUuidV4()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function convertToPrice($amount)
    {
        return number_format((double)$amount, 2, '.', '');
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        Log::warning(json_encode($request->all()));

        $requestIP = ip2long($request->ip());
        $ipRangeStart = ip2long('91.194.226.1');
        $ipRangeEnd = ip2long('91.194.227.254');

        if ($requestIP >= $ipRangeStart && $requestIP <= $ipRangeEnd) {
            if (!isset($request->id))
                return new Response('', 423);
            $order = Order::find($request->id);
            if (is_null($order))
                return new Response('', 423);

            switch ($request->status) {
                case Order::DOLYAMI_STATUS_REJECTED:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_REJECTED]);
                    break;
                case Order::DOLYAMI_STATUS_CANCELED:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_CANCELED]);
                    break;
                case Order::DOLYAMI_STATUS_APPROVED:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_APPROVED, 'payment_schedule' => $request->payment_schedule]);
                    break;
                case Order::DOLYAMI_STATUS_COMMITED:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_COMMITED]);
                    break;
                case Order::DOLYAMI_STATUS_COMPLETED:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_COMPLETED, 'paid_at' => now(), 'status' => Order::STATUS_PAID]);

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

                    \App\Jobs\FetchOrderInfo::dispatch($order, $uuidCdek);

//                    FetchCdekOrderInfo::dispatch($uuidCdek, $order, $packagesCdek, $order->price)
//                        ->delay(now()->addMinute());

                    break;
                case Order::DOLYAMI_STATUS_WAIT_FOR_COMMIT:
                    $dolyamiController = new DolyamiController();
                    $response = $dolyamiController->commitOrder($order);
                    if ($response['success']) {
                        $order->update(['payment_status' => Order::DOLYAMI_STATUS_WAIT_FOR_COMMIT, 'payment_schedule' => $request->payment_schedule]);
                    }
                    Log::info($response);
                    break;
                case Order::DOLYAMI_STATUS_WAITING_FOR_COMMIT:
                    $order->update(['payment_status' => Order::DOLYAMI_STATUS_WAITING_FOR_COMMIT]);
                    break;
            }
            return new Response('OK', 200);
        } else
            return new Response('', 423);
    }

    private function sendRequest(string $method, string $endpoint, array $data = [])
    {
        $headers = [
            "Content-Type: application/json",
            "X-Correlation-ID: " . $this->generateUuidV4(),
            "Authorization: Basic " . base64_encode("{$this->login}:{$this->password}")
        ];

        // Инициализация КУРЛА ыфв ФЫаепцуецу4пукер
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::$api_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
            $responseHeaders .= $header;
            return strlen($header);
        });
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // проверка крч на ключи
        if ($this->path_sert) {
            if (!file_exists($this->path_sert))
                throw new \Exception('Cert path did\'t exist:' . $this->path_sert);
            if (!file_exists($this->path_key))
                throw new \Exception('Key path did\'t exist:' . $this->path_key);
            if (!is_readable($this->path_sert))
                throw new \Exception('Can\'t read cert file:' . $this->path_sert);
            if (!is_readable($this->path_key))
                throw new \Exception('Can\'t read key file:' . $this->path_key);
            curl_setopt($ch, CURLOPT_SSLCERT, $this->path_sert);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->path_key);
        }
        // закидываем инфу в запрос
        if (!empty($data) || $method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ($this->debug) {
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);        // включаем вывод заголовков
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);   // включаем информацию о заголовках запроса
        }
        $out = curl_exec($ch);
        if ($this->debug) {
            dump('POST DATA', json_encode($data));
            $requestHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            dump($requestHeaders);
            dump(curl_getinfo($ch));
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Full CURL Request (including POST data):\n";
            echo htmlspecialchars($verboseLog);
            dump($verbose, $verboseLog);
            fclose($verbose);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $options = curl_getinfo($ch, CURLINFO_PRIVATE);

        Log::channel('dolyami')->info('CURL Options', [
            'options' => $options,
        ]);

        $response = json_decode($out, true);
        Log::channel('dolyami')->info($response);
        if ($code == 200) {
            return [
                'success' => true,
                'code' => $code,
                'data' => $response
            ];
        } else if ($code == 429) {
            $headers = $this->parseHeadersToArray($responseHeaders);
            sleep($headers['X-Retry-After']);
            return $this->sendPostRequest($endpoint, $data);
        } else {
            $result = [
                'success' => false,
                'code' => $code,
                'data' => [
                    'description' => isset($response['type']) && $response['type'] == 'error' ? $response['description'] : 'empty',
                    'message' => $response['message'] ?? 'empty',
                    'details' => !empty($response['details']) ? implode(array_map(function ($key, $value) {
                        return "$key - $value";
                    }, array_keys($response['details']), array_values($response['details']))) : 'empty'
                ]
            ];
            Log::channel('dolyami')->info($result);
        }
    }

    private function sendPostRequest(string $endpoint, array $data = [])
    {
        return $this->sendRequest('POST', $endpoint, $data);
    }

    private function sendGetRequest(string $endpoint)
    {
        return $this->sendRequest('GET', $endpoint);
    }

    private static function getProductsForOrder(Order $order, $product_type = Product::TYPE_PRODUCT)
    {
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
        if ($pointsAmount > 0 && $productsWithoutSaleAmount > 0) {
            $pointsWriteOff = $pointsAmount;
//            $pointsWriteOff = round($pointsAmount / $productsWithoutSaleAmount, 2);
        }

        foreach ($order->products as $product) {
            $db_product = Product::find($product['id']);
            $price = $order->type === Product::TYPE_CERTIFICATE
                ? $product['certificate']['price']
                : (isset($db_product->discount)
                    ? $db_product->getDiscountedPrice()
                    : $product['price']);
            // if ($order->use_certificate && $have_free_product) {
            //     $price = $order->price / ($products_count - 1);
            // }

            $totalPrice = static::convertToPrice($price);
            $minusOneItem = false;

            if ((!$db_product->discount || $db_product->discount == 0) && $totalPrice != 0) {
                if (isset($product['is_free']) && $product['is_free'] && $product['quantity'] == 1) {
                } else {
                    if ($pointsWriteOff > 0) {
                        $totalPrice -= $pointsWriteOff;
                    }

                    if ($promocodePercent) {
                        $totalPrice -= $totalPrice * ($promocodePercent / 100);
                    }

//                    if ($pointsWriteOff > 0) {
//                        if ($totalPrice > (static::convertToPrice($pointsWriteOff) / $product['quantity'])) {
//                            $totalPrice -= static::convertToPrice($pointsWriteOff) / $product['quantity'];
//                            $pointsWriteOff = 0;
//                        } else {
//                            $minusOneItem = true;
//                            $pointsWriteOff -= $totalPrice;
//                        }
//                    }
                }
            }

            $items[] = [
                'name' => (string)$product['name'],
                'price' => static::convertToPrice($totalPrice),
                'quantity' => (int)$minusOneItem ? $product['quantity'] - 1 : $product['quantity'],
                'receipt' => [
                    'tax' => 'none',
                    'payment_method' => 'full_payment',
                    'payment_object' => 'commodity',
                ],
            ];
        }

        $itemsFinalSum = 0;
        foreach ($items as $item) {
            if ($freeProductName && $freeProductName == $item['name']) {
                $item['quantity'] -= 1;
            }

            $itemsFinalSum += $item['price'] * $item['quantity'];
        }

        // if (static::convertToPrice($order->price) != $itemsFinalSum) {
        //     $differencePrice = static::convertToPrice($order->price) - $itemsFinalSum;

        //     $items[] = [
        //         'name' => 'Корректировка разницы',
        //         'price' => static::convertToPrice($differencePrice),
        //         'quantity' => 1,
        //         'receipt' => [
        //             'tax' => 'none',
        //             'payment_method' => 'full_payment',
        //             'payment_object' => 'commodity',
        //         ],
        //     ];
        // }

        if ($order->delivery_price > 0) {
            $items[] = [
                'name' => 'Доставка',
                'price' => static::convertToPrice($order->delivery_price),
                'quantity' => 1,
                'receipt' => [
                    'tax' => 'none',
                    'payment_method' => 'full_payment',
                    'payment_object' => 'commodity',
                ],
            ];
        }

        return $items;
    }

    private function getOrderInfo(string $id)
    {
        return $this->sendGetRequest("orders/$id/info");
    }

    public function createOrder(Order $order, $success_route, $failed_route)
    {

        $prepaid_amount = 0;

        $promocode = Promocode::where('code', $order->promocode)->first();
        $products_count = 0;
        foreach ($order->products as $_product1)
            $products_count += $_product1['quantity'];

        $storeSettings = StoreSetting::first();

        // Используется если товар будет помечен как бесплатный (используется при акции 1+1=3)
        foreach ($order->products as $_product) {
            if (isset($_product['is_free']) && $_product['is_free'] && isset($storeSettings) && $storeSettings->events['use_free_three_product']) {
                $db_product = Product::find($_product['id']);
                $price = ($order->type === Product::TYPE_CERTIFICATE)
                    ? $_product['certificate']['price']
                    : (isset($db_product->discount)
                        ? $db_product->getDiscountedPrice()
                        : $_product['price']);

                $prepaid_amount = $price;
            }
        }

        $params = [
            "fiscalization_settings" => [
                "type" => "enabled"
            ],
            'notification_url' => route('dolyami.webhook'),
            'fail_url' => $failed_route,
            'success_url' => $success_route,
            'order' => [
                'id' => $this->isProduction ? (string)$order->id : (string)rand(1000, 9999),
                'amount' => static::convertToPrice($order->price),
                'items' => static::getProductsForOrder($order),
            ],
            'client_info' => [
                'first_name' => $order->recipient_name,
                'last_name' => $order->recipient_last_name,
                'phone' => $order->recipient_phone,
                'email' => $order->recipient_email,
            ],
        ];

        if ($order->use_certificate) {
            if ($order->price > $order->cert_amount) {
                $prepaid_amount += $order->cert_amount;
            }
        }

        if ($prepaid_amount !== 0) {
            $params['order']['prepaid_amount'] = $prepaid_amount;
        }

        // TODO: костыль с корректировкой разницы
        $items = collect($params['order']['items']);
        $items->transform(fn($item) => $item['price'] === $items->max('price')
            ? [...$item, 'price' => static::convertToPrice($item['price'] - ($items->sum('price') - $params['order']['amount']))]
            : $item
        );
        $params['order']['items'] = $items->toArray();

        if ($this->debug) {
            dump($params);
        }
        return $this->sendPostRequest('orders/create', $params);
    }

    public function commitOrder(Order $order)
    {
        $params = [
            'amount' => static::convertToPrice($order->price),
            'items' => static::getProductsForOrder($order),
        ];
        return $this->sendPostRequest("orders/$order->id/commit", $params);
    }

    public function cancelOrder(Order $order)
    {
        $params = [
            'orderId' => $order->id,
        ];
        return $this->sendPostRequest("orders/$order->id/cancel");
    }

    public function refundOrder(Order $order)
    {
        $params = [
            'amount' => static::convertToPrice($order->price),
            'returned_items' => static::getProductsForOrder($order)
        ];
        return $this->sendPostRequest("orders/$order->id/refund", $params);
    }

    public function correctionOrder(Order $order, $products)
    {
        $params = [
            'amount' => static::convertToPrice($order->price),
        ];
        return $this->sendPostRequest("orders/$order->id/correction", $params);
    }

    public function completeDelivery(Order $order, $products)
    {
        $params = [];
        return $this->sendPostRequest("orders/$order->id/complete_delivery", $params);
    }

    protected function parseHeadersToArray($rawHeaders)
    {
        $lines = explode("\r\n", $rawHeaders);
        $headers = [];
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            list($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
        return $headers;
    }
}

