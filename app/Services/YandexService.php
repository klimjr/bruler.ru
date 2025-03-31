<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Сервис для взаимодействия с yandexPay
 */
class YandexService
{
    /**
     * Ключ апи
     *
     * @var string
     */
    private string $apiKey;

    /**
     * URL, куда направляются запросы
     *
     * @var string
     */
    private string $baseUrl;

    /**
     * @var
     */
    protected static $instance;

    /**
     * @return YandexService
     */
    public static function getInstance(): YandexService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Конструктор
     *
     * Задает настройки на основе параметров в services
     */
    private function __construct()
    {
        if (!empty(config('services.yandex_pay.is_test_mode'))) {
            $this->apiKey = config('services.yandex_pay.merchant_id');
            $this->baseUrl = config('services.yandex_pay.base_test_url');
        } else {
            $this->apiKey = config('services.yandex_pay.api_key');
            $this->baseUrl = config('services.yandex_pay.base_url');
        }
    }

    /**
     * Создать заказ
     *
     * Отправляет данные заказа в сервис yandex
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data) : array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Api-Key ' . $this->apiKey,
        ])
            ->post($this->baseUrl . '/api/merchant/v1/orders', $data);
        if ($response->successful()) {
            $json = $response->json();
            return [
                'success' => true,
                'data' => $json
            ];
        } else {
            $json = $response->json();
            \Log::channel('yandex')->error($json);
            return [
                'success' => false,
                'data' => $json
            ];
        }
    }

    /**
     * Получить заказ
     *
     * Возвращает данные заказ их yandex
     *
     * @param string $orderId
     * @return array
     */
    public function getOrder(string $orderId) : array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Api-Key ' . $this->apiKey,
        ])
            ->get($this->baseUrl . '/api/merchant/v1/orders/' . $orderId);
        if ($response->successful()) {
            $json = $response->json();
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
