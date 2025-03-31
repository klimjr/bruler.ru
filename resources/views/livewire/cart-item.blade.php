<div
    x-data='{
    isModalOpen: false,
    hiddenItems: {},
    cart: [],
    is_free: false,
    removeYMProduct(quantity, id) {
        let product = {{ $product }}
        let price = {{ isset($product->discount) ? $product->getDiscountedPrice() : $product->price }}
        this.hiddenItems[id] = true

        if (window.dataLayer) {
            window.dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                        "remove": {
                        "products": [
                            {
                            id: product.id,
                            name: product.name_en ?? "",
                            price: price,
                            quantity: quantity
                            }
                        ]
                    }
                }
            })
        }
    },
    removeOneYMProduct(quantity, id) {
        this.hiddenItems[id] = quantity > 1 ? false : true
        let product = {{ $product }}
        let price = {{ isset($product->discount) ? $product->getDiscountedPrice() : $product->price }}

        if (window.dataLayer) {
            window.dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                    "remove": {
                        "products": [
                            {
                            id: product.id,
                            name: product.name_en ?? "",
                            price: price,
                            quantity: 1
                            }
                        ]
                    }
                }
            })
        }
    },
    addOneYMProduct() {
        let product = {{ $product }}
        let price = {{ isset($product->discount) ? $product->getDiscountedPrice() : $product->price }}

        if (window.dataLayer) {
            window.dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                    "add": {
                    "products": [
                        {
                        id: product.id,
                        name: product.name_en ?? "",
                        price: price,
                        quantity: 1
                        }
                    ]
                    }
                }
            })
        }
    }
}'>

    <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" x-cloak @click="isModalOpen = false"></div>

    @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
        <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-50 flex items-center justify-center"
            x-cloak>
            <div id="profile-edit" class="relative bg-white rounded-[15px] px-[35px] py-[30px] w-[325px]">
                <div class="absolute top-[15px] right-[15px] cursor-pointer" @click="isModalOpen = false">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.60743 18.3925L18.3925 6.60743M6.60742 6.60742L18.3925 18.3925" stroke="#757575"
                            stroke-width="1.875" stroke-linecap="square" />
                    </svg>
                </div>

                <div class="text-xl font-normal text-center">Доступные размеры:</div>

                <div class="flex flex-col items-center">
                    <livewire:product-variant-selector :selected-color="$variant->color->id"
                        wire:key="product-variant-selector-{{ $product->id }}" :product="$product" />
                    <div>
                        <div class="text-xs md:text-sm text-[#757575] mt-2 underline cursor-pointer">
                            Размерная сетка
                        </div>
                    </div>

                    <x-button-black class="mt-3" wire:click="changeVariant" @click="isModalOpen = false">
                        Выбрать
                    </x-button-black>
                </div>
            </div>
        </div>
    @endif

    <div class="flex border-t-2 border-[#757575] gap-4 py-7" x-show="!hiddenItems[{{ $cart_id }}]">
        <a href="{{ $product->getRouteUrl() }}" class="w-[140px] h-[160px]">
            @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                <img class="object-cover w-full h-full" src="{{ $variant->getImageUrlAttribute() }}"
                    alt="{{ $product->name }}" />
            @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                <img class="object-cover w-full h-full" src="{{ $certificate['image'] }}"
                    alt="{{ $product->name }}" />
            @endif
        </a>
        <div class="flex flex-col w-full">
            <div class="flex w-full h-full flex-col space-y-4 justify-between">
                <div class="flex justify-between items-start space-x-2">
                    <a href="{{ $product->getRouteUrl() }}" class="flex flex-col">
                        <p class="main-text">{{ $product->name_en }}</p>
                    </a>
                    <button class="md:hidden" wire:click="removeProduct('{{ $cart_id }}')"
                        @click="hiddenItems[{{ $cart_id }}] = true">
                        <x-icons.close />
                    </button>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-1">
                        @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                            <div class="text-sm text-[#757575]">Размер: <span
                                    class="text-black">{{ $variant->size->name }}</span></div>
                            <div class="cursor-pointer" @click="isModalOpen = true">
                                <x-icons.pen-cart />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="">
                    <div class="flex items-center gap-3 select-none">
                        <div @click="removeOneYMProduct({{ $quantity }}, {{ $cart_id }})"
                            wire:click="decrementQuantity">
                            <x-icons.round-minus class="cursor-pointer" />
                        </div>
                        <div class="!text-[12px] md:!text-[22px]">
                            @if ($is_free && $quantity !== 1)
                                {{ $quantity - 1 }}
                            @else
                                {{ $quantity }}
                            @endif
                        </div>
                        <div @click="addOneYMProduct({{ $quantity }})" wire:click="incrementQuantity"
                            x-bind:class="{ 'hidden': {{ $quantity }} >= {{ $maxQuantity }} }">
                            <x-icons.round-plus class="cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-start">
                    <div class="">
                        @if ($is_free && $quantity === 1)
                            <span>
                                <span class="text-[#757575] text-lg font-medium">Итого: </span><span
                                    class="text-xl font-semibold">0₽</span>
                            </span>
                        @elseif($is_free && $quantity !== 1)
                            @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                                @if (isset($product->discount) && $product->discount != 0)
                                    <span class="text-[#757575] text-lg font-medium">Итого: </span>
                                    <span
                                        class="text-[#D0021B] text-xl font-semibold">{{ number_format($product->getDiscountedPrice(), 0, '.', '.') }}₽</span>
                                    <s
                                        class="text-xl font-semibold">{{ number_format($product->price, 0, '.', '.') }}₽</s>
                                @else
                                    <span>
                                        <span class="text-[#757575] text-lg font-medium">Итого:</span>
                                        <span
                                            class="text-xl font-semibold">{{ number_format($product->price, 0, '.', '.') }}₽</span>
                                    </span>
                                @endif
                            @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                                <span
                                    class="text-[#757575] text-lg font-medium">Итого:</span>{{ number_format($certificate['price'], 0, '.', '.') }}₽
                            @endif
                        @else
                            @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                                @if (isset($product->discount) && $product->discount != 0)
                                    <span class="text-[#757575] text-lg font-medium">Итого: </span>
                                    <span
                                        class="text-[#D0021B] text-xl font-semibold">{{ number_format($product->getDiscountedPrice() * $quantity, 0, '.', '.') }}₽</span>
                                    <s
                                        class="text-xl font-semibold">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽</s>
                                @else
                                    <span>
                                        <span class="text-[#757575] text-lg font-medium">Итого:
                                        </span>
                                        <span
                                            class="text-xl font-semibold">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽
                                        </span>
                                    </span>
                                @endif
                            @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                                <span class="text-[#757575] text-lg font-medium">Итого:
                                </span>{{ number_format($certificate['price'] * $quantity, 0, '.', '.') }}₽
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-[25px] items-end justify-end cursor-pointer hidden md:flex"
            wire:click="removeProduct('{{ $cart_id }}')"
            @click="removeYMProduct({{ $quantity }}, {{ $cart_id }})">
            <x-icons.delete-cart />
        </div>
    </div>
</div>
