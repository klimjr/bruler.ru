<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class DostavistaApi
{
    private $url;
    private $baseUrl;
    private $testUrl;
    private $address_sklad;
    private $phone_sklad;
    private $is_test_mode;

    private $token;

    public function __construct()
    {
        $this->is_test_mode = config('services.dostavista.is_test_mode');
        $this->baseUrl = config('services.dostavista.base_url');
        $this->testUrl = config('services.dostavista.test_url');
        $this->url = $this->is_test_mode ? $this->testUrl : $this->baseUrl;
        $this->token = $this->is_test_mode ? config('services.dostavista.api_test_token') : config('services.dostavista.api_token');
        $this->address_sklad = config('services.dostavista.address_sklad');
        $this->phone_sklad = config('services.dostavista.phone_sklad');
    }

    public function getDeliveryIntervals($date)
    {
        $response = Http::withHeaders([
            'X-DV-Auth-Token' => $this->token,
        ])->get($this->url . 'delivery-intervals', [
            'date' => $date->format('Y-m-d'),
        ]);
        return $response->json();
    }

    public function calculateOrder($order)
    {
        $url = $this->url . 'calculate-order';
        $address = $order['address'];
        $phone = $order['recipient_phone'];
        $startDateTime = $order['start_datetime'];
        $finishDateTime = $order['finish_datetime'];
        $weight = round($order['pcs'] * 0.5, 0, PHP_ROUND_HALF_UP);

        $requestData = [
            'type' => 'same_day',
            'total_weight_kg' => $weight,
            'matter' => 'Одежда',
            'points' => [
                [
                    'address' => $this->address_sklad,
                    'contact_person' => [
                        'phone' => $this->phone_sklad
                    ],
//                    'required_start_datetime' => $startDateTime,
//                    'required_finish_datetime' => $finishDateTime
                ],
                [
                    'address' => $address,
                    'contact_person' => [
                        'phone' => $phone
                    ],
//                    'client_order_id' => $order['id'],
                    'required_start_datetime' => $startDateTime,
                    'required_finish_datetime' => $finishDateTime
                ]
            ]
        ];
        $response = Http::withHeaders([
            'X-DV-Auth-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);
        return [
            'response' => $response->json(),
            'requestData' => $requestData
            ];
    }

    public function createOrder($order)
    {
        $url = $this->url . 'create-order';
        $response = Http::withHeaders([
            'X-DV-Auth-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->post($url, $order);
        return $response->json();
    }

}
