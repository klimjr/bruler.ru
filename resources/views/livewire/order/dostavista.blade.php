<div>
    @if($addressDostavista)
        <div class="mb-4">
            <div class="text-sm mb-2">Дата доставки</div>
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-wrap gap-2">
                    @foreach($deliveryDates as $key => $day)
                        <button type="button" wire:click="setDeliveryDay({{ $key }})"
                            @class([
                                'text-sm px-3 py-2 border rounded-xl border-black',
                                'bg-black text-white' => $key === $selectedDate,])
                        >{{ \Carbon\Carbon::parse($day)->format('d.m') }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div>
            <div class="text-sm mb-2">Выберете время доставки</div>
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-wrap gap-2">
                    @if(count($deliveryIntervals))
                        @foreach($deliveryIntervals as $key => $interval)
                            <button type="button" wire:click="setDeliveryInterval({{ $key }})"
                                @class([
                                    'text-sm px-3 py-2 border rounded-xl border-black h-10',
                                    'bg-black text-white' => $key === $selectedInterval,])
                            >
                                {{ \Carbon\Carbon::parse($interval['required_start_datetime'])->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($interval['required_finish_datetime'])->format('H:i') }}
                            </button>
                        @endforeach
                    @else

                        <div class="text-sm px-3 py-2 border rounded-xl border-black h-10">Нет доступных временных
                            интервалов
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
