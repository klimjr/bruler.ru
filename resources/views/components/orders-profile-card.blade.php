<div {{ $attributes }} class="flex flex-col space-y-3 w-full">
    <div class="flex justify-between items-center space-x-2">
        <span class="main-text">{{ $order->created_at->locale('ru')->isoFormat('D MMMM Y') }}</span>
        <span class="small-text !text-gray">Заказ № {{ $order->id }}</span>
    </div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end space-x-0 md:space-x-2">
        <div class="grid grid-cols-3 gap-4 w-full">
            @foreach ($order->products as $product)
                @for ($i = 0; $i < $product['quantity']; $i++)
                    <div
                        class="col-span-1 overflow-hidden w-full h-[97px] min-h-[97px] md:min-w-full md:min-h-[155px] md:w-full md:h-[155px]">
                        <img alt="{{ $product['name'] }}" src="{{ $product['image_url'] }}"
                            class="w-full h-full object-contain" />
                    </div>
                @endfor
            @endforeach
        </div>
        <div
            class="flex flex-row w-full lg:min-w-[350px] justify-between md:flex-col space-y-0 space-x-2 mt-8 md:mt-0 md:space-x-0 md:space-y-3 items-end">
            @if ($order->price)
                <p class="price-small">Сумма: {{ $order->price }} ₽</p>
            @endif
            <a href="{{ $url }}">
                <x-button
                    class="flex justify-center items-center space-x-1 md:space-x-2 !w-[96px] !min-w-[96px] !text-[12px] !rounded-md md:!text-[20px] !min-h-[22px] !px-1 !py-0 !h-[22px] md:!h-[29px] md:!w-[215px]">
                    <span>подробнее</span>
                    <x-icons.arrow-right-medium class="!h-3 !w-3 !md:h-7 !md:w-7" />
                </x-button>
            </a>
        </div>
    </div>
</div>
<div class="h-[1px] md:h-[2px] w-full bg-primary mt-2 md:mt-10 mb-1 md:mb-3"></div>
