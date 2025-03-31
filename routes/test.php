<?php

use App\Http\Controllers\CDEKController;
use App\Http\Controllers\DolyamiController;
use App\Http\Controllers\TinkoffController;
use App\Jobs\CheckYandexPayStatus;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Models\User;
use App\Notifications\CertificateNotificataion;
use App\Notifications\OrderConfirmed;

Route::group(['prefix' => 'test'], function () {
    Route::get('dolyami/{id?}', function ($id) {
        $defaultId = 1;
        $id = $id ?? $defaultId;
        $order = \App\Models\Order::find($id);
        $dolyami = new DolyamiController(true);
        dump($order);
        dump($dolyami);
        $dolyami->createOrder($order, 'success_order', 'failed_order');

    });
    Route::get('ttt', function () {

        $order_id = 12922;
        $order = \App\Models\Order::find($order_id);
//        $totalprice = $order->price_with_promocode;
        $totalprice = $order->price;
        $success_route = 'success_order';
        $failed_route = 'failed_order';
        $delivery_price = $order->delivery_price;
        $email = $order->recipient_email;
        $phone = $order->recipient_phone;
        $db_promocode = null;
        if (isset($order->promocode)) {
            $promocode = Promocode::where('code', $order->promocode)->first();
            if ($promocode) {
                $promocode = $order->promocode;
                $db_promocode = $promocode;
            }
        }
//    dd($totalprice,
//        'Оплата заказа',
//        $order->id,
//        $success_route,
//        $failed_route,
//        $delivery_price,
//        $email,
//        $phone,
//        $order->products,
//        $db_promocode,
//        $order);
        $tinkoff = TinkoffController::createPayment(
            $totalprice,
            'Оплата заказа',
            $order->id,
            $success_route,
            $failed_route,
            $delivery_price,
            $email,
            $phone,
            $order->products,
            $db_promocode,
            $order
        );
    });
    Route::get('settings', function () {
        $storeSettings = StoreSetting::first();
        dd(collect($storeSettings->events['delivery'])
            ->where('id', 'cdek')
            ->keys()
            ->first());

    });
    Route::get('yandex/{order_id}', function ($order_id) {
        $order = \App\Models\Order::find($order_id);
        if ($order) {
            CheckYandexPayStatus::dispatch($order)->delay(now()->addMinutes());
        } else {
            dd("Order not found");
        }
    });
    Route::get('cdek_track/{order_id}', function ($order_id) {
        $order = Order::findOrFail($order_id);
        dump($order);
        if (
            $order->delivery_type !== 3 &&
            $order->delivery_type !== 0 &&
            $order->track_number === null
            ) {
            dump('Доставка СДЭК');
            $uuidCdek = CDEKController::afterPayment($order);
            dump($uuidCdek);
            if(request()->has('job')) {
                \App\Jobs\FetchOrderInfo::dispatch($order, $uuidCdek);
//                FetchCdekOrderInfo::dispatch($uuidCdek, $order, $packagesCdek, $order->price);
            } else {
                $fetchOrderInfo = new \App\Jobs\FetchOrderInfo($order, $uuidCdek);
                $fetchOrderInfo->handle();
            }
            dd('Ок');
        }
    });

    Route::get('mail/order/{order_id}', function ($order_id) {
        $order = Order::findOrFail($order_id);
        $name = $order->recipient_name;
        $notificationUser = new User();
        $notificationUser->email = env('TEST_EMAIL');
        $notificationUser->notify(new OrderConfirmed($name, $order));
    });

    Route::get('mail/cert/{order_id}', function ($order_id) {
        $order = Order::findOrFail($order_id);

        if(request()->has('create')) {
            $certificate = new \App\Models\Certificate();
            $certificate->order_id = $order->id;
            $certificate->target_email = $order->target_email;
            $certificate->remains = $order->certificate['certificate']['price'];
            $certificate->expires_at = now()->addYear();
            $certificate->code = $certificate->code = 'GIFT' . $order->id;
            $certificate->save();
        } else {
            $cert = $order->certificate;
            $certificate = App\Models\Certificate::find($cert['id']);
        }


        if($certificate) {
            $email = $order->target_email ?? $order->recipient_email;
            $notificationUser = new User();
            $notificationUser->email = $email;
            $certificateImage = 'https://bruler.ru/storage/products/01HYBFWRCX92BYVFVTKPE3JN0R.jpg';

            if (array_key_exists('certificate', $cert)) {
                $certificateImage = $cert['certificate']['image'];
            }

            $notificationUser->notify(new CertificateNotificataion($cert->code, $certificateImage));
            dd('Сообщение отправлено на: '. $email);

        } else {
            dd('Сертификат не найден' );
        }
    });


    Route::get('/jobs', 'App\Http\Controllers\JobMonitorController@index')->name('admin.jobs');
    Route::get('/jobs/test', 'App\Http\Controllers\JobTestController@test')->name('admin.jobs.test');
    Route::get('/jobs/status', 'App\Http\Controllers\JobTestController@status')->name('admin.jobs.status');

});
