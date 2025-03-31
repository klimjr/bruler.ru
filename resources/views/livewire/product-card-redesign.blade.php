<div class="relative group flex flex-col font-normal {{ $sizeContainer }}">
    <div class="z-10">
        <div class="absolute left-0 space-y-0 md:space-y-2">
            @if ($isNew)
                <div class="w-fit flex items-center gap-x-[4px] bg-[#EBF7FF] px-[8px] py-[6px]">
                    <span class="text-[#1C85F6] text-xs">Новое</span>
                </div>
            @endif

            @if ($isSoldOut)
                <div class="w-fit flex items-center gap-x-1 bg-black px-2 py-[6px]">
                    <span class="text-white text-xs uppercase">Sold Out</span>
                </div>
            @endif

            @if (isset($product->discount) && $product->discount != 0)
                <div class="w-fit flex items-center gap-x-1 bg-[#FFE8E8] px-2 py-[6px]">
                    <x-icons.new.sale />
                    <span class="text-[#CD0C0C] text-xs">{{ $product->discount }}%</span>
                </div>
            @endif
        </div>
    </div>

    <livewire:fire
        wire:key="fire-{{ $product->id }}"
        button-classes="md:group-hover:block absolute top-2 right-2 md:top-4 md:right-4 z-10 max-md:h-5 max-md:w-5 text-color-111"
        :is-active="$isActive"
        :is-favourite="$isFavourite"
        :product="$product"
    />

    <div class="group relative {{ $sizeLink }}">
        <a
            href="{{ $url }}"
            x-data="productCardData"
            @click.stop="clickYMProduct(@js($product))"
        >
            @if ($image_back)
                <img class="absolute top-0 left-0 object-contain w-full h-full opacity-0 transition-opacity duration-500 group-hover:opacity-100 border-0"
                     onerror="this.style.border = 'none'; this.style.display = 'none';"
                     loading="lazy"
                     decoding="async"
                     src="{{ $image_back }}"
                >
                <img class="absolute top-0 left-0 object-contain w-full h-full transition-opacity duration-500 group-hover:opacity-0 border-0"
                     onerror="this.style.border = 'none'; this.style.display = 'none';"
                     loading="lazy"
                     decoding="async"
                     src="{{ $image }}"
                >
            @else
                <img
                    class="object-contain w-full h-full border-0"
                    loading="lazy"
                    decoding="async"
                    src="{{ $image }}"
                >
            @endif
            <div class="absolute inset-0 bg-[#0000000A]"></div>
        </a>
    </div>

    <div
        class="{{ $isActive ? '' : 'md:hidden'  }} md:absolute md:group-hover:flex w-full md:bottom-0 md:bg-[#E2E2E2E5] px-1 mt-1 md:px-2 md:pt-2 md:pb-3 flex-col space-y-1.5 md:space-y-2"
    >
        <div class="flex flex-col md:flex-row items-start justify-between max-md:space-y-1.5">
            <div class="text-xs md:text-lg md:leading-[1.4] font-bold md:font-normal md:min-h-[50px] md:mr-4">{{ $product->name_en }}</div>
            <div class="flex items-center text-xs md:text-base gap-1.5 whitespace-nowrap">
                @if (isset($product->discount) && $product->discount != 0)
                    <div class="flex items-center gap-x-2 justify-center">
                        <span
                            class="text-[#CD0C0C]">{{ number_format($product->getDiscountedPrice(), 0, ' ', ' ') }} ₽
                        </span>
                        <s class="text-color-111">
                            {{ number_format($product->price, 0, ' ', ' ') }} ₽
                        </s>
                    </div>
                @else
                    <div class="text-black">{{ number_format($product->price, 0, ' ', ' ') }} ₽</div>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between text-xs md:text-base md:min-h-[34px]">
            <ul class="flex items-center gap-x-2">
                @foreach ($sizes as $id => $size)
                    <li
                        @if ($size['available'])
                        wire:click="selectSize({{ $id }})"
                        class="text-black cursor-pointer relative @if ($selectedVariant == $id) underline @else underline-animated @endif"
                        @else class="text-color-111" @endif>
                        {{ $size['name'] }}
                    </li>
                @endforeach
            </ul>
            @if (!$isSoldOut)
                <button x-data="{ hover: false }"
                        @if (!$selectedVariant) @mouseenter="hover = true" @mouseleave="hover = false" @endif
                        @if ($selectedVariant && $sizes[$selectedVariant]['inCart']) disabled @else wire:click="addToCart" @endif
                        class="hidden @if ($selectedVariant && $sizes[$selectedVariant]['inCart']) bg-black text-white @endif md:block border text-sm border-black rounded-[12px] px-3 py-1.5">
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

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productCardData', () => ({
            clickYMProduct(product) {
                if (window.dataLayer && window.dataLayer instanceof Array) {
                    window.dataLayer.push({
                        ecommerce: {
                            currencyCode: "RUB",
                            click: {
                                products: [
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
            }
        }));
    });
</script>
