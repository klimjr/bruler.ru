<?php

namespace App\Livewire;

use App\Http\Controllers\CDEKController;
use App\Jobs\FetchCdekOrderInfo;
use App\Models\Certificate;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Notifications\CertificateNotificataion;
use App\Notifications\OrderConfirmed;
use App\Services\DostavistaApi;
use App\Services\YandexPayService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SuccessOrder extends Component
{

    public $order_id;
    public $type;
    public $target_email;
    public $certificate;
    public $used_certificate;
    public $bonus;
    public $total_price;
    public $user_id;

    public function boot()
    {

        $this->order_id = request('order_id'); // id заказа
        $this->type = request('type'); // Тип заказа

        $this->user_id = request('user_id'); // id пользователя
        $this->total_price = request('total_price'); // Общая сумма заказа


        $this->target_email = request('target_email'); // email получателя сертификата
        $this->certificate = request('certificate'); // Сертификат, который использовали
        $this->used_certificate = request('used_certificate'); // Использовал ли сертификат
        $this->bonus = request('bonus'); // Использовал ли бонусы





        // НАЧИСЛЕНИЕ БАЛЛОВ
        if (isset($this->user_id)) {
            $userAuth = User::find($this->user_id);

            if ($userAuth) {
                $sumPoints = $this->total_price * ($userAuth->cashback() / 100);
                $userAuth->points += $sumPoints;
                $userAuth->save();
            }
        }
        // ---

        // Получение информации о заказе
        $orderInfo = Order::find($this->order_id);

        // Проверка статуса заказа в Yandex Pay
        if($orderInfo->payment_type == 2) {
            $ya = new YandexPayService();
            $OrderStatus = $ya->chkOrderStatus($this->order_id);

            $orderStatusMap = [
                'SUCCESS' => 'paid',
                'FAIL' => 'fail',
                'PENDING' => 'pending',
            ];
            if (isset($OrderStatus['paymentStatus'])) {

                $updateData = [
                    'payment_status' => $OrderStatus['paymentStatus'],
                    'payment_id' => $OrderStatus['operationId'],
                    'paid_at' => now(),
                    'status' => Order::STATUS_PAID
                ];

                if (isset($orderStatusMap[$OrderStatus['paymentStatus']])) {
                    $updateData['status'] = $orderStatusMap[$OrderStatus['paymentStatus']];
                }
                $orderInfo->update($updateData);
                // Отправка письма
                $user = new User();
                $user->email = $orderInfo->recipient_email;
                $user->notify(new OrderConfirmed($orderInfo->recipient_name, $orderInfo));

                if ($orderInfo->type !== Product::TYPE_CERTIFICATE) {
                    // TODO: заменить ID
                    $uuidCdek = '';
                    if ($orderInfo->delivery_type != 3 && $orderInfo->delivery_type != 0) {
                        $uuidCdek = CDEKController::afterPayment($orderInfo);
                    }
//                    $fetchOrderInfo = new \App\Jobs\FetchOrderInfo($orderInfo, $uuidCdek);
//                    $fetchOrderInfo->handle();
                    \App\Jobs\FetchOrderInfo::dispatch($orderInfo, $uuidCdek);

                }

            } else {
                Log::warning('Unknown payment status', ['status' => $OrderStatus['paymentStatus']]);
                return response()->json(['message' => 'Unknown payment status'], 400);
            }
        }
        // Сохранение доставки
        if ($orderInfo->delivery_type == 0 && $orderInfo->type != 'certificate') {

            if ($orderInfo->delivery_info) {
                $this->saveDelivery($orderInfo);
            } else {
                Log::driver('dostavista')->warning('No delivery info', ['order_id' => $this->order_id]);
            }
        }

        // Вычитание количества товара
        $orderInfo = Order::find($this->order_id);
        if (isset($orderInfo->products)) {
            foreach ($orderInfo->products as $productInfo) {
                if (isset($productInfo['variant'])) {
                    $variant = ProductVariant::find($productInfo['variant']);
                    $variant->amount -= $productInfo['quantity'];
                    $variant->save();
                }
            }
        }
        // ---

        // Создание сертификата после покупки

        if (
            $this->type === \App\Models\Product::TYPE_CERTIFICATE &&
            isset($this->order_id) &&
            isset($this->certificate) &&
            isset($this->target_email) &&
            !Certificate::where('target_email', $this->target_email)
                ->where('order_id', $this->order_id)
                ->exists()
        ) {
            $certificate = new Certificate();
            $certificate->order_id = $this->order_id;
            $certificate->target_email = $this->target_email;
            $certificate->remains = (array_key_exists('certificate', $this->certificate)) ? $this->certificate['certificate']['price'] : $this->certificate['price'];
            $certificate->expires_at = now()->addYear();
            $certificate->code = $certificate->code = 'GIFT' . $this->order_id;
            $certificate->save();


            $notificationUser = new User();
            $notificationUser->email = $this->target_email;
            $certificateImage = 'https://bruler.ru/storage/products/01HYBFWRCX92BYVFVTKPE3JN0R.jpg';

            if (array_key_exists('certificate', $this->certificate)) {
                $certificateImage = $this->certificate['certificate']['image'];
            }
            $notificationUser->notify(new CertificateNotificataion($certificate->code, $certificateImage));
        }
        // Использовал сертификат
        if (
            $this->type === \App\Models\Product::TYPE_PRODUCT &&
            isset($this->order_id) &&
            isset($this->used_certificate) &&
            isset($this->total_price)
        ) {
            $db_cert = Certificate::where('code', $this->used_certificate)->first();

            $db_cert->update([
                'remains' => $db_cert->remains <= $this->total_price ? $db_cert->remains -= $db_cert->remains : $db_cert->remains = -$this->total_price,
                'used_at' => now(),
            ]);
        }

        if (
            isset($this->order_id) &&
            isset($this->bonus) &&
            isset($this->total_price) &&
            isset($this->user_id)
        ) {
            $userAuth = User::find($this->user_id);

            if ($userAuth) {
                if ($userAuth->points >= $this->bonus) {
                    $userAuth->points -= $this->bonus;
                    $userAuth->save();
                }
            }
        }
    }

    private function saveDelivery($order)
    {
        if ($order->delivery_info) {
            $dostavistaApi = new DostavistaApi();
            $dostavistaOrder = $dostavistaApi->createOrder($order->delivery_info);
            $order->update([
                "track_number" => $dostavistaOrder['order']["order_id"],
            ]);
            $order->save();
            if (
                isset($dostavistaOrder["errors"]) &&
                count($dostavistaOrder["errors"]) > 0
            ) {
                \Log::driver('dostavista')->error($dostavistaOrder);
            }
        }
    }

    public function render()
    {
        session()->forget("cart");
        return view('livewire.success_order')->extends('layouts.app');
    }
}


