<div>
    @if($main->products_main_text || $main->products_span_text)
        <div class="flex flex-col items-center text-center mt-4 md:mt-6">
            <span class="text-[#999999]">{{ $main->products_span_text }}</span>
            <h3 class="text-[18px] md:text-[28px]">{{ $main->products_main_text }}</h3>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-y-4 gap-x-1 md:grid-cols-4 md:gap-y-2 md:gap-x-2 mt-4 md:mt-6">
        @foreach ($products as $product)
            @if ($product->show)
                <livewire:product-card-redesign :product="$product" />
            @endif
        @endforeach
    </div>

    <div class="flex justify-center w-full mt-[16px] md:mt-[24px]">
        <a href="{{ route('catalog') }}"
            class="px-[24px] py-[8px] rounded-[16px] border-[1px] duration-300 hover:bg-black hover:text-white">Смотреть все</a>
    </div>
</div>
