<?php

namespace App\Livewire;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Notifications\CertificateNotificataion;
use App\Notifications\RecoveryCodeNotification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SuccessOrderRedesign extends Component
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
        $this->order_id = request()->query('order_id');
        $this->type = request()->query('type');
        $this->target_email = request()->query('target_email');
        $this->certificate = request()->query('certificate');
        $this->used_certificate = request()->query('used_certificate');
        $this->bonus = request()->query('bonus');
        $this->user_id = request()->query('user_id');
        $this->total_price = request()->query('total_price');

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

    public function render()
    {
        return view('livewire.success-order-redesign')->extends('layouts.app');
    }
}
