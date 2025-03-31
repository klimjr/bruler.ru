<?php

namespace App\Livewire\Profile;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Services\DostavistaApi;

class Profile extends Component
{
    /** @var string */
    public $name = '';

    /** @var string */
    public $last_name = '';

    /** @var string */
    public $phone = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $city = '';

    /** @var string */
    public $birthday = '';
    /** @var string */
    public $formatted_birthday = '';

    /** @var string */
    public $image = '';

    /** @var array */
    public $favourites = [];
    /** @var array */
    public $orders_history = [];
    public $accept_pp = true;

    public $isSaved = false;
    public $isValidationError = false;
    public $points = 0;
    public $cashback = 3;
    public $currentOrders = [];

    // Общие статусы доставки
    const DELIVERY_STATUS_NEW = 0;          // Новый/Создан
    const DELIVERY_STATUS_PROCESSING = 1;   // В обработке/На складе
    const DELIVERY_STATUS_ON_WAY = 2;       // В пути
    const DELIVERY_STATUS_READY = 3;        // Готов к выдаче
    const DELIVERY_STATUS_DELIVERED = 4;    // Доставлен
    const DELIVERY_STATUS_CANCELED = 5;     // Отменен
    const DELIVERY_STATUS_DELAYED = 6;      // Отложен

    public function save()
    {
        $this->validate([
            'name' => ['required'],
            'last_name' => ['required'],
            'phone' => ['required', 'min:11'],
            'email' => ['required', 'email']
        ]);

        if (!preg_match('/^\+7[\s\-]?\(?(\d{3})\)?[\s\-]?(\d{3})[\s\-]?(\d{2})[\s\-]?(\d{2})$/', $this->phone)) {

            $this->addError('phone', 'Телефон не валидный');
            $this->isValidationError = true;
            return;
        }

        if (!$this->accept_pp) {
            $this->addError('accept_pp', 'Необходимо принять соглашение');
            $this->isValidationError = true;
            return;
        }

        $authUser = Auth::user();
        $existsUser = User::where('email', $this->email)->first();

        if ($existsUser && $existsUser->email !== $authUser->email) {
            $this->addError('email', 'Такая почта уже используется');
            $this->isValidationError = true;
            return;
        }

        $authUser->update([
            'email' => $this->email,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
        ]);

        $this->isSaved = true;
        $this->isValidationError = false;
    }

    public function logout()
    {
        return redirect()->route('logout');
    }

    public function mount()
    {
        if (Auth::user()) {
            $this->name = Auth::user()->name;
            $this->last_name = Auth::user()->last_name;
            $this->phone = Auth::user()->phone;
            $this->city = Auth::user()->city;
            $this->birthday = Auth::user()->birthday;
            $this->formatted_birthday = $this->formatBirthday($this->birthday);
            $this->image = Auth::user()->image;
            $this->email = Auth::user()->email;
            $this->favourites = Auth::user()->favourites;

            if (Auth::user()->points) {
                $this->points = Auth::user()->points;
            }

            foreach (Auth::user()->orders as $order) {
                if ($order->status != 'not_cofirmed' && $order->status != 'created') {
                    $this->orders_history[] = $order;
                }
            }

            $this->cashback = Auth::user()->cashback();
            $this->checkOrderStatus();
        }
    }

    public function checkOrderStatus()
    {
        $orders = Auth::user()->orders
            ->whereNotNull('track_number')
            ->sortByDesc('created_at');
        foreach ($orders as $order) {
            if ($order->delivery_type == 0) {
                $this->checkDostavistaStatus($order);
            } else {
                $this->checkCDEKStatus($order);
            }
        }
    }

    public function checkDostavistaStatus($order)
    {
        if (!in_array($order->status, ['paid_receipt', 'paid', 'shipping'])) {
            return;
        }

        try {
            $token = config('services.dostavista.is_test_mode') ? config('services.dostavista.api_test_token') : config('services.dostavista.api_token');
            $url = config('services.dostavista.is_test_mode') ? config('services.dostavista.test_url') : config('services.dostavista.base_url');

            $response = Http::withHeaders([
                'X-DV-Auth-Token' => $token,
            ])->get($url . 'orders?order_id=' . $order->track_number);

            if ($response && isset($response['orders'][0])) {
                $orderStatus = $response['orders'][0]['status'];
                $deliveryStatus = $this->mapDostavistaStatus($orderStatus);
                $this->currentOrders[] = [
                    'idOrder' => $order->id,
                    'deliveryType' => 'dostavista',
                    'deliveryStatus' => $deliveryStatus,
                    'deliveryStatusDescription' => $this->getDeliveryStatusDescription($deliveryStatus)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Dostavista API error: ' . $e->getMessage());
        }
    }

    private function mapDostavistaStatus(string $statusCode): int
    {
        $statusMap = [
            'new' => self::DELIVERY_STATUS_NEW,           // Ожидает одобрения
            'draft' => self::DELIVERY_STATUS_NEW,         // Черновик
            'available' => self::DELIVERY_STATUS_PROCESSING,   // Одобрен, доступен курьерам
            'reactivated' => self::DELIVERY_STATUS_PROCESSING, // Повторно активирован
            'active' => self::DELIVERY_STATUS_ON_WAY,     // Выполняется курьером
            'completed' => self::DELIVERY_STATUS_DELIVERED, // Выполнен
            'canceled' => self::DELIVERY_STATUS_CANCELED,  // Отменен
            'delayed' => self::DELIVERY_STATUS_DELAYED     // Отложен
        ];

        return $statusMap[$statusCode] ?? self::DELIVERY_STATUS_NEW;
    }

    public function checkCDEKStatus($order)
    {
        if (!in_array($order->status, ['paid_receipt', 'paid', 'shipping']) || !$order->track_number) {
            return;
        }
        try {
            $url = config('services.cdek.api_test_mode') ? config('services.cdek.api_test_url') : config('services.cdek.api_url');
            $response = Http::cdek()->get($url . '/v2/orders?im_number='.$order->track_number);
            if ($response->successful()) {
                $orderData = $response->json();

                if (isset($orderData['entity']['statuses'][0])) {
                    $orderStatus = $orderData['entity']['statuses'][0];
                    $deliveryStatus = $this->mapCDEKStatus($orderStatus['code']);

                    $this->currentOrders[] = [
                        'idOrder' => $order->id,
                        'deliveryType' => 'cdek',
                        'deliveryStatus' => $deliveryStatus,
                        'deliveryStatusDescription' => $this->getDeliveryStatusDescription($deliveryStatus)
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('CDEK API error: ' . $e->getMessage());
        }
    }

    private function mapCDEKStatus(string $statusCode): int
    {
        $statusMap = [
            'ACCEPTED' => self::DELIVERY_STATUS_NEW,
            'CREATED' => self::DELIVERY_STATUS_NEW,
            'RECEIVED_AT_SHIPMENT_WAREHOUSE' => self::DELIVERY_STATUS_PROCESSING,
            'PROCESSING' => self::DELIVERY_STATUS_PROCESSING,
            'ON_WAREHOUSE' => self::DELIVERY_STATUS_PROCESSING,
            'DEPARTED' => self::DELIVERY_STATUS_ON_WAY,
            'IN_TRANSIT' => self::DELIVERY_STATUS_ON_WAY,
            'ACCEPTED_AT_PICK_UP_POINT' => self::DELIVERY_STATUS_READY,
            'READY_FOR_PICKUP' => self::DELIVERY_STATUS_READY,
            'DELIVERED' => self::DELIVERY_STATUS_DELIVERED,
            'CANCELED' => self::DELIVERY_STATUS_CANCELED
        ];

        return $statusMap[$statusCode] ?? self::DELIVERY_STATUS_PROCESSING;
    }

    private function getDeliveryStatusDescription(int $status): string
    {
        $descriptions = [
            self::DELIVERY_STATUS_NEW => 'Заказ создан',
            self::DELIVERY_STATUS_PROCESSING => 'Заказ в обработке',
            self::DELIVERY_STATUS_ON_WAY => 'Заказ в пути',
            self::DELIVERY_STATUS_READY => 'Заказ готов к выдаче',
            self::DELIVERY_STATUS_DELIVERED => 'Заказ доставлен',
            self::DELIVERY_STATUS_CANCELED => 'Заказ отменен',
            self::DELIVERY_STATUS_DELAYED => 'Заказ отложен'
        ];

        return $descriptions[$status] ?? 'Неизвестный статус';
    }

    private function formatBirthday($birthday)
    {
        return $birthday ? date('d.m.Y', strtotime($birthday)) : '';
    }

    public function getInitials()
    {
        $firstName = $this->name ? mb_substr($this->name, 0, 1) : '';
        $lastName = $this->last_name ? mb_substr($this->last_name, 0, 1) : '';

        return mb_strtoupper($firstName . $lastName);
    }

    public function render()
    {
        return view('profile.index')->extends('layouts.app');
    }
}
