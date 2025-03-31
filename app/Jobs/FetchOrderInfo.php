<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

require_once 'sms.ru.php';

class FetchOrderInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed
     */
    private mixed $url;

    public function __construct(protected $order, protected $uuid = '')
    {
        $this->url = config('services.cdek.api_test_mode') ? config('services.cdek.api_test_url') : config('services.cdek.api_url');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->uuid) {
            $responseOrderInfo = Http::cdek()->get($this->url . '/v2/orders/' . $this->uuid);
            $arrayCdekinfo = $responseOrderInfo->json();
            if (isset($arrayCdekinfo['entity']['cdek_number'])) {
                $this->trackNumber = $arrayCdekinfo['entity']['cdek_number'];
                $order = Order::find($this->order->id);
                if ($order) {
                    $order->track_number = $arrayCdekinfo['entity']['cdek_number'];
                    $order->save();
                    $this->order = $order;
                }
            }
        }
        $textMessage = $this->getMessage();
        $formattedPhone = preg_replace('/[^0-9]/', '', $this->order->recipient_phone);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer 3fe78523c81e45a5abe6e8443e404bdd',
            ])->post('https://api.wazzup24.com/v3/message', [
                'channelId' => 'ef1d939d-f1ef-4501-bc20-b89ad8c25f4d',
                'chatId' => $formattedPhone,
                'chatType' => 'whatsapp',
                'text' => $textMessage
            ]);


            $log = print_r($response->json(), true);
            file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
            $log = print_r($response->status(), true);
            file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

        if ($this->order->track_number) {
            if ($response->status() != 201 && $this->order->track_number) {
                $name = $this->order->recipient_name;
                $orderNum = $this->order->id;
                $this->trackNumber = $this->order->track_number;

                $smsru = new \SMSRU('D05AE0C5-9F79-BE28-C6A7-620E26F23C9B');
                $smsMessage = "Добрый день, $name!

Благодарим вас за заказ №$orderNum на сайте Brûler d'Amour.
Трек номер для отслеживания СДЭК: $this->trackNumber";

                $data = new \stdClass();
                $data->to = $formattedPhone;
                $data->text = $smsMessage;

                $log = print_r($data, true);
                file_put_contents(__DIR__ . '/smslog.txt', $log . PHP_EOL, FILE_APPEND);
                $sms = $smsru->send_one($data);

                if ($sms->status == "OK") {
                    $log = print_r('SMS отправлено на: ' . $data->to, true);
                    file_put_contents(__DIR__ . '/smslog.txt', $log . PHP_EOL, FILE_APPEND);
                } else {
                    $log = print_r('Ошибка: ' . $sms->status_text, true);
                    file_put_contents(__DIR__ . '/errsmslog.txt', $log . PHP_EOL, FILE_APPEND);
                }
            }
        }
    }

    public function getMessage()
    {
        // Initialize orderItems array
        $orderItems = [];
        $orderItemsString = '';

        // For delivery types 1 and 2 (CDEK)
        if ($this->order->delivery_type !== 3 && $this->order->delivery_type !== 0) {
            foreach ($this->order->products as $product) {
                $packagesCdek['items'][] = [
                    "id" => (string)$product['id'],
                    "name" => $product['name'],
                    "UnitName" => "шт.",
                    "price" => $product['price'],
                    "quantity" => $product['quantity']
                ];
            }

            if(isset($packagesCdek)) {
                foreach ($packagesCdek['items'] as $index => $item) {
                    $orderItems[] = ($index + 1) . ". " . $item['name'] . ", " . $item['quantity'] . ' ' . $item['UnitName'];
                }
                $orderItemsString = implode("\n", $orderItems);
            }
        }
        // For delivery types 0 and 3
        else {
            foreach ($this->order->products as $index => $product) {
                $orderItems[] = ($index + 1) . ". " . $product['name'] . ", " . $product['quantity'] . ' шт.';
            }
            $orderItemsString = implode("\n", $orderItems);
        }

        $name = $this->order->recipient_name;
        $orderNum = $this->order->id;

        $storeSettings = StoreSetting::first();
        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"], fn($payment, $key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"], fn($delivery, $key) => [$key => $delivery["label"]]);

        $deliveryType = $deliveryOptions[$this->order->delivery_type];
        $paymentType = $paymentOptions[$this->order->payment_type];

        $deliveryInfo = $deliveryType;
        $orderPrice = $this->order->price;
        $trackNumber = $this->order->track_number;

        if ($this->order->delivery_type === 0) {
            $deliveryInterval = '';
            if (isset($this->order->delivery_info['points'][1])) {
                $startTime = \Carbon\Carbon::parse($this->order->delivery_info['points'][1]['required_start_datetime'])->format('H:i');
                $endTime = \Carbon\Carbon::parse($this->order->delivery_info['points'][1]['required_finish_datetime'])->format('H:i');
                $deliveryInterval = $startTime . ' - ' . $endTime;
            }

            $textMessage = "Добрый день, $name!
Благодарим вас за заказ №$orderNum на сайте Brûler d'Amour. Мы очень ценим, что вы выбрали наш бренд!

Состав заказа:
$orderItemsString

Доставка: $deliveryInfo
Интервал доставки: $deliveryInterval
Способ оплаты: $paymentType
Сумма к оплате: $orderPrice руб.


Подскажите, пожалуйста, от кого узнали о нашем бренде?

С уважением,
команда Brûler d'Amour";
        }

        if ($this->order->delivery_type === 2 || $this->order->delivery_type === 1) {
            $textMessage = "Добрый день, $name!
Благодарим вас за заказ №$orderNum на сайте Brûler d'Amour. Мы очень ценим, что вы выбрали наш бренд!

Состав заказа:
$orderItemsString

Доставка: $deliveryInfo
Способ оплаты: $paymentType
Сумма к оплате: $orderPrice руб.

Трек номер для отслеживания: $trackNumber

Подскажите, пожалуйста, от кого узнали о нашем бренде?

С уважением,
команда Brûler d'Amour";
        }

        if ($this->order->delivery_type === 3) {
            $textMessage = "Добрый день, $name!
Благодарим вас за заказ №$orderNum на сайте Brûler d'Amour. Мы очень ценим, что вы выбрали наш бренд!

Состав заказа:
$orderItemsString

Доставка: самовывоз
Самовывоз по адресу: 2-я Бауманская ул.,9/23c3 (БЦ Суперметалл) 2 этаж офис 3203
Контакт telegram @bruler_support
Просьба связаться за 30 минут до визита.
График с 10:00 до 19:00
Сб, Вс - выходной

Мы сообщим, когда Ваш заказ будет собран и готов к выдаче в шоуруме.

Способ оплаты: $paymentType
Сумма к оплате: $orderPrice руб.


Подскажите, пожалуйста, от кого узнали о нашем бренде?

С уважением,
команда Brûler d'Amour";

        }

        return $textMessage;
    }
}
