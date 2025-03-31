<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

require_once 'sms.ru.php';

class FetchCdekOrderInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uuid;
    protected $order;
    protected $packages;
    protected $orderPrice;
    protected $trackNumber;
    private $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uuid = '', $order, $packages, $orderPrice)
    {
        $this->uuid = $uuid;
        $this->order = $order;
        $this->packages = $packages;
        $this->orderPrice = $orderPrice;
        $this->url = config('services.cdek.api_test_mode') ? config('services.cdek.api_test_url') : config('services.cdek.api_url');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->uuid != '') {
            $responseOrderInfo = Http::cdek()->get($this->url . '/v2/orders/' . $this->uuid);
            $arrayCdekinfo = $responseOrderInfo->json();

            if (isset($arrayCdekinfo['entity']['cdek_number'])) {
                $this->trackNumber = $arrayCdekinfo['entity']['cdek_number'];
                $order = Order::find($this->order->id);
                if ($order) {
                    $order->track_number = $arrayCdekinfo['entity']['cdek_number'];
                    $order->save();
                }
            }
        }

        $name = $this->order->recipient_name;
        $formattedPhone = preg_replace('/[^0-9]/', '', $this->order->recipient_phone);
        $orderNum = $this->order->id;
        $orderItems = [];
        $paymentType = '';
        $deliveryType = '';
        $deliveryInfo = '';

        foreach ($this->packages['items'] as $index => $item) {
            $orderItems[] = ($index + 1) . ". " . $item['name'] . ", " . $item['quantity'] . ' ' . $item['UnitName'] . ", " . $item['price'] . " руб.";
        }

        $orderItemsString = implode("\n", $orderItems);

        switch ($this->order->payment_type) {
            case \App\Models\Order::PAYMENT_TYPE_CARD:
                $paymentType = 'картой онлайн';
                break;
            case \App\Models\Order::PAYMENT_TYPE_DOLYAMI:
                $paymentType = 'долями';
                break;
            case \App\Models\Order::PAYMENT_TYPE_CASH:
                $paymentType = 'наличными при получении';
                break;
        }

        switch ($this->order->delivery_type) {
            case \App\Models\Order::DELIVERY_TYPE_CDEK:
                $deliveryType = 'курьерская доставка CDEK';
                break;
            case \App\Models\Order::DELIVERY_TYPE_CDEK_PVZ:
                $deliveryType = 'до ПВЗ CDEK';
                break;
            case \App\Models\Order::DELIVERY_TYPE_PICKUP:
                $deliveryType = 'самовывоз';
                break;
        }

        $deliveryInfo = $this->order->address . ', ' . $deliveryType;
        $textMessage = "Добрый день, $name!
Благодарим вас за заказ №$orderNum на сайте Brûler d'Amour. Мы очень ценим, что вы выбрали наш бренд!

Состав заказа:
$orderItemsString

Доставка: $deliveryInfo
Способ оплаты: $paymentType
Сумма к оплате: $this->orderPrice руб.

Трек номер для отслеживания: $this->trackNumber

Подскажите, пожалуйста, от кого узнали о нашем бренде?

С уважением,
команда Brûler d'Amour";
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

        if ($response->status() != 201) {
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
