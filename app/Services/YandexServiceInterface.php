<?php

namespace App\Services;

class YandexServiceInterface implements PaymentServiceInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.yandex_pay.api_key');
        $this->baseUrl = config('services.yandex_pay.base_url');
    }

    public function initPayment(int $orderId, array $productItems = [])
    {

    }
}
