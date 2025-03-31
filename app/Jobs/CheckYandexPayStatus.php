<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CheckYandexPayStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $tries = 0;
    public $maxExceptions = 0;
    public $timeout = 0;
    private $startTime;

    private $merchantId;
    private $apiKey;
    private $baseUrl;
    private $testMode;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->startTime = now();
    }

// Метод определяет предельное время выполнения задачи
    public function retryUntil()
    {
        return now()->addMinutes(30);
    }

    public function handle()
    {
        $this->setupConfiguration();

// Проверяем, не прошло ли 30 минут
        if (now()->diffInMinutes($this->startTime) >= 30) {
            $this->order->update([
                'status' => Order::STATUS_NOT_CONFIRMED,
                'payment_status' => 'TIMEOUT'
            ]);
            \Log::driver('yandex')->warning("Payment check timeout for order #{$this->order->id} after 30 minutes");
            return;
        }

        try {
            $response = $this->checkOrderStatus();

            if ($response->successful()) {
                $orderStatus = $response->json()['data']['order']['paymentStatus'];
                switch ($orderStatus) {
                    case 'CAPTURED':
                        if ($this->order->status !== Order::STATUS_PAID) {
                            $this->order->update([
                                'status' => Order::STATUS_PAID,
                                'payment_status' => $orderStatus,
                                'paid_at' => now()
                            ]);
                            \Log::driver('yandex')->info("Payment successful for order #{$this->order->id}");
                        }
                        return; // Завершаем job при успешной оплате

                    case 'PENDING':
                        if ($this->order->payment_status !== 'PENDING') {
                            $this->order->update([
                                'payment_status' => $orderStatus,
                                'status' => Order::STATUS_NOT_CONFIRMED
                            ]);
                        }
                        $this->release(3); // Повторить через 3 секунды
                        break;

                    default:
                        $this->order->update([
                            'payment_status' => $orderStatus,
                            'status' => Order::STATUS_NOT_CONFIRMED
                        ]);
                        $this->release(3);
                        break;
                }
            } else {
                \Log::driver('yandex')->error("Error checking Yandex Pay status for order #{$this->order->id}: " . $response->body());
                $this->release(3);
            }
        } catch (\Exception $e) {
            \Log::driver('yandex')->error("Exception while checking Yandex Pay status for order #{$this->order->id}: " . $e->getMessage());
            $this->release(3);
        }
    }

    private function setupConfiguration()
    {
        $this->merchantId = config('services.yandex_pay.merchant_id');
        $this->testMode = config('services.yandex_pay.test_mode', true);

        if ($this->testMode) {
            $this->apiKey = '6d92a6fe-6c06-48e8-9d14-3b080bf649cf';
            $this->baseUrl = 'https://sandbox.pay.yandex.ru/api/merchant/v1/orders';
        } else {
            $this->apiKey = config('services.yandex_pay.api_key');
            $this->baseUrl = 'https://pay.yandex.ru/api/merchant/v1/orders';
        }
    }

    private function checkOrderStatus()
    {
        return Http::withHeaders([
            'Authorization' => 'API-Key ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->get("{$this->baseUrl}/{$this->order->id}");
    }

// Обработка неудачного выполнения задачи
    public function failed(\Throwable $exception)
    {
        \Log::driver('yandex')->error("Job failed for order #{$this->order->id}: " . $exception->getMessage());

        $this->order->update([
            'status' => Order::STATUS_NOT_CONFIRMED,
            'payment_status' => 'FAILED'
        ]);
    }
}
