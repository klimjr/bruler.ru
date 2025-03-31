<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BoxberryController extends Controller
{
    public function __construct()
    {
        $this->client = new Client();
    }

    public function getShippingCost($idPoint, $orderSum, $packages)
    {
        $destination = [
            "token" => config('services.boxberry.api'),
            "method" => 'DeliveryCalculation',
            "DeliveryType" => "1",
            "TargetStart" => "00414",
            "TargetStop" => $idPoint,
            "OrderSum" => $orderSum,
            "BoxSizes" => $packages
        ];

        try {
            $response = $this->client->post(config('services.boxberry.url'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $destination
            ]);
    
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function createShipment($orderId, $itemsPriceInfo, $idPoint, $customerInfo, $packages, $notice = '', $cash = false)
    {
        $data = [
            'token' => config('services.boxberry.api'),
            'method' => 'ParselCreate',
            'sdata' => json_encode([
                "order_id" => (string)$orderId,
                "price" => $itemsPriceInfo['price'],
                "payment_sum" => $cash ? $itemsPriceInfo['price'] + $itemsPriceInfo['delivery_sum'] : 0,
                "delivery_sum" => $itemsPriceInfo['delivery_sum'],
                "vid" => "1",
                "shop" => [
                    "name" => $idPoint,
                    "name1" => "00414"
                ],
                "customer" => $customerInfo,
                "items" => $packages['items'],
                "notice" => $notice,
                "weights" => $packages['weights'],
                "issue" => "0"
            ])
        ];

        try {
            $response = $this->client->post(config('services.boxberry.url'), [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
