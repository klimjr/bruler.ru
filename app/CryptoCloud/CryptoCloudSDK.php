<?php

namespace App\CryptoCloud;

class CryptoCloudSDK
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1dWlkIjoiTWpZNE16QT0iLCJ0eXBlIjoicHJvamVjdCIsInYiOiI5NDNjYzAxNTM1YzFiNjFjOGFlNDY0MTRmNTgzNWEwOGY2ZmU4YzcwNjMxYTI4ODljMmZlODM5NTk5MTQ2MjZlIiwiZXhwIjo4ODEyOTE1NTkxMH0.n22JoHiw8wvSc4cjTkEfqCP8Te5OjEI5qayOfCiC3h0';
        $this->baseUrl = "https://api.cryptocloud.plus/v2/";
    }

    private function sendRequest($endpoint, $method = "POST", $payload = null)
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            "Authorization: Token " . $this->apiKey,
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function createInvoice($invoiceData)
    {
        return $this->sendRequest("invoice/create", "POST", $invoiceData);
    }

    public function cancelInvoice($uuid)
    {
        return $this->sendRequest("invoice/merchant/canceled", "POST", ["uuid" => $uuid]);
    }

    public function listInvoices($startDate, $endDate, $offset = 0, $limit = 10)
    {
        return $this->sendRequest("invoice/merchant/list", "POST", [
            "start" => $startDate,
            "end" => $endDate,
            "offset" => $offset,
            "limit" => $limit
        ]);
    }

    public function getInvoiceInfo($uuids)
    {
        return $this->sendRequest("invoice/merchant/info", "POST", ["uuids" => $uuids]);
    }

    public function getBalance()
    {
        return $this->sendRequest("merchant/wallet/balance/all", "POST");
    }

    public function getStatistics($startDate, $endDate)
    {
        return $this->sendRequest("invoice/merchant/statistics", "POST", [
            "start" => $startDate,
            "end" => $endDate
        ]);
    }
}