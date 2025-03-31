@php
    $deliveryInfo = $getRecord()->delivery_info;
    if (isset($deliveryInfo['points'][1])) {
        $deliveryPoint = $deliveryInfo['points'][1];
        $startTime = \Carbon\Carbon::parse($deliveryPoint['required_start_datetime'])->format('H:i');
        $endTime = \Carbon\Carbon::parse($deliveryPoint['required_finish_datetime'])->format('H:i');
        $address = $deliveryPoint['address'];
    }
@endphp

@if(isset($deliveryPoint))
    <div class="space-y-4">
        <div class="flex flex-col gap-1">
            <span class="font-medium text-gray-500">Адрес доставки:</span>
            <span class="text-gray-900">{{ $address }}</span>
        </div>

        <div class="flex flex-col gap-1">
            <span class="font-medium text-gray-500">Интервал доставки:</span>
            <span class="text-gray-900">{{ $startTime }} - {{ $endTime }}</span>
        </div>
    </div>
@else
    <div class="text-gray-500">
        Информация о доставке отсутствует
    </div>
@endif