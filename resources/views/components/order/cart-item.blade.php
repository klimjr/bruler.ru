@props(['product'])
<div class="border-b border-grey-200 pb-3">
    <div class="grid grid-cols-[90px_auto_24px] gap-3">
        <a href="#" class="w-[90px] h-[124px] bg-grey-200">
            <img
                class="object-scale-down w-full h-full"
                src="{{ asset('storage/'.$product['image']) }}"
                alt="{{ $product['name'] }}"
            />
        </a>

        <div class="flex flex-col w-full h-full ">
            <a href="{{ '#' }}" class="inline-block text-[18px] font-bold mb-2">
                {{ $product['name'] }}
            </a>

            <div class="mb-3">
                @if(isset($product['old_price']))
                    <span class="inline-block text-[18px] text-red">{{ $product['price'] }} ₽</span>
                    <span class="inline-block text-[18px] line-through text-color-111">{{ $product['old_price'] }} ₽</span>
                @else
                    <span class="inline-block text-[18px]">{{ $product['price'] }} ₽</span>
                @endif
            </div>

            <div class="flex items-center mb-1">
                <div class="text-black mr-1">
                    Размер:<span class="text-black">{{ $product['size'] }}</span>
                </div>
            </div>

            <div class="flex items-center">
                <div class="text-black mr-1">Кол-во:</div>
                <span>{{ $product['quantity'] }}</span>
            </div>

        </div>

    </div>
</div>
