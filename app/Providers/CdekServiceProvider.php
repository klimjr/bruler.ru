<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class CdekServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Перед выполнением запроса к CDEK API
        Http::macro('cdek', function () {
            $tokenExpiresAt = cache('cdek_token_expires_at');

            // Проверяем, не истек ли срок действия токена
            if (!$tokenExpiresAt || now()->greaterThan($tokenExpiresAt)) {
                $client = new Client(['base_uri' => 'https://api.cdek.ru']);
                $data = [
                    'client_id' => config('services.cdek.account'),
                    'client_secret' => config('services.cdek.password'),
                    'grant_type' => 'client_credentials'
                ];

                $headers = ['Accept' => 'application/json'];

                $response = $client->request('POST', '/v2/oauth/token', [
                    'form_params' => $data,
                    'headers' => $headers
                ]);

                $body = $response->getBody();
                $decodedResponse = json_decode($body, true);

                if ($decodedResponse) {
                    $token = $decodedResponse['access_token'];
                    $expiresIn = $decodedResponse['expires_in'];

                    cache(['cdek_token' => $token], now()->addSeconds($expiresIn));
                    cache(['cdek_token_expires_at' => now()->addSeconds($expiresIn)]);
                }
            }

            return Http::withHeaders([
                'Authorization' => 'Bearer ' . cache('cdek_token'),
            ]);
        });
    }
}
