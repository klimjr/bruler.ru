<div class="relative group flex flex-col md:h-[31vw] font-normal">
    <div class="z-10">
        <div class="absolute left-0 space-y-0 md:space-y-2">
            <div
                class="@if (!$isNew) hidden @endif w-fit flex items-center gap-x-[4px] bg-[#EBF7FF] px-[8px] py-[6px]">
                <span class="text-[#1C85F6] text-xs">Новое</span>
            </div>
            @if($badge)
            <div
                 class="@if (!$badge) hidden @endif w-fit flex items-center gap-x-[4px] px-[8px] py-[6px]"
                 style="background-color: {{ $badge['color'] }}"
                >
                <span class="text-white text-xs">{{ $badge['name'] }}</span>
            </div>
            @endif

            <div
                class="@if (!$isSoldOut) hidden @endif w-fit flex items-center gap-x-[4px] bg-[#131313] px-[8px] py-[6px]">
                <span class="text-white text-xs uppercase">Sold Out</span>
            </div>

            {{-- <div x-cloak x-show="preorder" :style="{ opacity: preorder ? '100' : '0' }">
                <x-icons.stripes.re class="w-[78px] md:w-auto" />
            </div> --}}

            <div
                class="@if (!isset($product->discount) || $product->discount == 0) hidden @endif w-fit flex items-center gap-x-[4px] bg-[#FFE8E8] px-[8px] py-[6px]">
                <x-icons.new.sale />
                <span class="text-[#CD0C0C] text-xs">{{ $product->discount }}%</span>
            </div>
        </div>
    </div>

    <livewire:fire
        button-classes="md:hidden md:group-hover:block absolute top-[7.5px] md:top-[17px] right-[7.5px] md:right-[17px] z-10"
        :product="$product" />

    <div class="group w-full h-[240px] md:h-full relative">
        <a href="{{ $url }}" @click="clickYMProduct()"
            x-data='{
        clickYMProduct() {
            let product = {{ $product }}
            window.dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                    "click": {
                        "products": [
                            {
                                id: product.id,
                                name: product.name_en ?? "",
                                price: product.price
                             }
                        ]
                    }
                }
            })
        }
     }'>
            @if ($image_back)
                <img class="absolute top-0 left-0 object-contain w-full h-full opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                    src="{{ $image_back }}">
                <img class="absolute top-0 left-0 object-contain w-full h-full transition-opacity duration-500 group-hover:opacity-0"
                    src="{{ $image }}">
            @else
                <img class="object-contain w-full h-full" src="{{ $image }}">
            @endif
            <div class="absolute inset-0 bg-[#0000000A]"></div>
        </a>
    </div>

    <div
        class="md:absolute md:hidden md:group-hover:flex w-full md:bottom-0 md:bg-[#E2E2E2E5] px-[8px] pt-[8px] pb-[12px] flex-col gap-y-[8px]">
        <div class="flex flex-col md:flex-row items-start justify-between">
            <div class="text-xs md:text-lg font-bold md:font-normal md:max-w-[16vw]">{{ $product->name_en }}</div>
            <div class="flex items-center text-xs md:text-base gap-1.5 whitespace-nowrap">
                @if (isset($product->discount) && $product->discount != 0)
                    <div class="flex items-center gap-x-[8px] justify-center">
                        <span
                            class="text-[#CD0C0C]">{{ number_format($product->getDiscountedPrice(), 0, ' ', ' ') }}₽</span>
                        <s class="text-[#999999]">
                            {{ number_format($product->price, 0, ' ', ' ') }}₽
                        </s>
                    </div>
                @else
                    <div class="text-[#131313]">{{ number_format($product->price, 0, ' ', ' ') }}₽</div>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between text-xs md:text-base">
            <ul class="flex items-center gap-x-2">
                @foreach ($sizes as $id => $size)
                    <li @if ($size['available']) wire:click="selectSize({{ $id }})"
                            class="text-[#131313] cursor-pointer relative @if ($selectedVariant == $id) underline @else underline-animated @endif"
                    @else class="text-[#999999]" @endif>
                        {{ $size['name'] }}
                    </li>
                @endforeach
            </ul>
            @if (!$isSoldOut)
                <button x-data="{ hover: false }"
                    @if (!$selectedVariant) @mouseenter="hover = true" @mouseleave="hover = false" @endif
                    @if ($selectedVariant && $sizes[$selectedVariant]['inCart']) disabled @else wire:click="addToCart" @endif
                    class="hidden @if ($selectedVariant && $sizes[$selectedVariant]['inCart']) bg-black text-white @endif md:block border-[1px] text-sm border-black rounded-[12px] px-[12px] py-[6px]">
                    @if ($product->type == 'certificate')
                        <a href="{{ $url }}">Выбрать номинал</a>
                    @else
                        @if ($selectedVariant && $sizes[$selectedVariant]['inCart'])
                            <span>В корзине</span>
                        @else
                            <span x-show="!hover">В корзину</span>
                            <span x-show="hover">Выберите размер</span>
                        @endif
                    @endif
                </button>
            @endif
        </div>
    </div>
</div>
