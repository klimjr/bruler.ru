<?php

namespace App\Livewire\Order;

use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Carbon\Carbon;
use Livewire\Component;
use App\Services\DostavistaApi;

class Dostavista extends Component
{
    #[Reactive]
    public $addressDostavista;
    public $pcs;
    #[Reactive]
    public $phone;
    private $dostavistaApi;
    public $selectedInterval = null;
    public $selectedDate = 0;
    public array $deliveryIntervals;
    public array $deliveryDates = [];

    public function mount($addressDostavista, $pcs, $phone)
    {
        $this->addressDostavista = $addressDostavista;
        $this->pcs = $pcs;
        $this->phone = $phone;

        $this->deliveryDates = $this->getDeliveryDateRange();
        $this->upDeliveryIntervals($this->deliveryDates[0]);
    }

    #[On('address-update')]
    public function addressUpdate()
    {
        $this->selectedInterval = null;
    }

    #[On('phone-update')]
    public function phoneUpdate($phone)
    {
        $this->phone = $phone;
    }

    private function getDeliveryDateRange()
    {
        $dates = [];
        $date = new \DateTime();
        $count = 0;

        while (count($dates) < 2) {
            if ($date->format('N') < 6) { // 1 (Monday) to 5 (Friday)
                $dates[] = clone $date;
            }
            $date->modify('+1 day');
            $count++;

            if ($count > 7) break; // Защита от бесконечного цикла
        }

        return $dates;
    }

    public function filterDeliveryIntervalsByWorkingHours(
        array  $deliveryIntervals,
               $date,
        string $startWorkingHour = '10',
        string $endWorkingHour = '19'
    ): array
    {
        $date = Carbon::parse($date);
        return collect($deliveryIntervals)
            ->filter(function ($interval) use ($date, $startWorkingHour, $endWorkingHour) {
                $startTime = Carbon::parse($interval['required_start_datetime']);
                $finishTime = Carbon::parse($interval['required_finish_datetime']);

                // Проверка, что интервалы находятся в пределах заданной даты
                if ($startTime->toDateString() !== $date->toDateString() || $finishTime->toDateString() !== $date->toDateString()) {
                    return false;
                }

                $startHours = (int)$startTime->format('H');
                $finishHours = (int)$finishTime->format('H');

                return ($startHours >= (int)$startWorkingHour && $startHours < (int)$endWorkingHour) &&
                    ($finishHours > (int)$startWorkingHour && $finishHours <= (int)$endWorkingHour);
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.order.dostavista', [

        ]);
    }

    #[On('reset-params')]
    public function resetParams()
    {
        $this->selectedInterval = null;
        $this->selectedDate = null;
        $this->dispatch('dostavista-error', '');
    }

    public function setDeliveryInterval($interval): void
    {
        $this->selectedInterval = $interval;
        $interval = $this->deliveryIntervals[$interval];
        $order = [
            'pcs' => $this->pcs,
            'recipient_phone' => $this->phone,
            'address' => 'Москва, ' . $this->addressDostavista,
            'start_datetime' => $interval['required_start_datetime'],
            'finish_datetime' => $interval['required_finish_datetime'],
        ];
        $dostavistaApi = new DostavistaApi();
        $response = $dostavistaApi->calculateOrder($order);
        $dostavistaOrder = $response['response'];
        $order = $response['requestData'];
        if (isset($dostavistaOrder['warnings']) && count($dostavistaOrder['warnings']) > 0) {
            if (isset($dostavistaOrder['parameter_warnings']['points'][1]['contact_person']['phone'][0]) == 'invalid_phone') {
                $this->dispatch('dostavista-error', 'Неверный формат телефона');
            }
        }
        $this->dispatch('resetErrors');
        if (isset($dostavistaOrder['errors']) && count($dostavistaOrder['errors']) > 0) {
            if (isset($dostavistaOrder['parameter_errors']['points'][1]['address'][0]) == 'coordinates_out_of_bounds') {
                $this->dispatch('dostavista-address-error', 'Введен неверный адрес доставки');
            } else {
//                address_not_found
                $this->dispatch('dostavista-error', 'Неизвестная ошибка. Обратитесь в поддержку.');
            }
        } else {
            $this->dispatch('set-delivery-price', price: $dostavistaOrder['order']['payment_amount']);
            $this->dispatch('create-dostavista-order', $order);
        }
    }

    private function upDeliveryIntervals($date)
    {
        $this->dostavistaApi = new DostavistaApi();
        $deliveryIntervals = $this->dostavistaApi->getDeliveryIntervals($date);
        $deliveryIntervals = $deliveryIntervals['delivery_intervals'];
        $this->deliveryIntervals = $this->filterDeliveryIntervalsByWorkingHours($deliveryIntervals, $date);
    }

    public function setDeliveryDay($date)
    {
        $this->selectedDate = $date;
        $this->upDeliveryIntervals($this->deliveryDates[$date]);
    }
}
