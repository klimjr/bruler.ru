<div class="text-center md:text-start" id="add-to-cart-button"
     x-data='{
    isModalOpen: false,
    openModalVariants(cartAction) {
        this.isModalOpen = true;
        document.getElementById("select-variant-btn").setAttribute("wire:click", cartAction);
    },
    addToDataLayer() {
        if (window.dataLayer) {
            window.dataLayer.push({
                "ecommerce": {
                "currencyCode": "RUB",
                "add": {
                    "products": [
                        {
                            "id": {{ $product->id }},
                            "name": "{{ str_replace(['"', "'"], ['\"', "\'"], $product->name_en) }}",
                            "price": {{ $product->price }},
                            "quantity": 1
                        }
                    ]
                }
                }
            });
        }
    }
 }'>
    <div x-show="isModalOpen" class="fixed inset-0 bg-black-opacity z-40" x-cloak @click="isModalOpen = false"></div>

    <div
        wire:ignore x-show="isModalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-50 flex items-center justify-center"
        x-cloak
    >
        <div id="profile-edit"
             class="relative bg-white rounded-[15px] px-[35px] w-full py-[30px] @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT) max-w-[350px] @else max-w-[426px] @endif">
            <div class="absolute top-[15px] right-[15px] cursor-pointer" @click="isModalOpen = false">
                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.60743 18.3925L18.3925 6.60743M6.60742 6.60742L18.3925 18.3925" stroke="#757575"
                          stroke-width="1.875" stroke-linecap="square" />
                </svg>
            </div>

            @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                <div class="text-xl font-normal text-center">Доступные размеры:</div>

                <div class="flex flex-col items-center">
                    <livewire:product-variant-selector-redesign
                        :selected-color="$color"
                        wire:key="product-variant-selector-{{ $product->id }}"
                        :product="$product"
                    />

                    <x-button-black id="select-variant-btn" class="mt-3" @click="isModalOpen = false">
                        Выбрать
                    </x-button-black>
                </div>
            @endif

            @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                <livewire:product-certificate-selector wire:key="product-certificate-selector-{{ $product->id }}"
                                                       :product="$product" />

                <x-button-black id="select-variant-btn" class="mt-3" @click="isModalOpen = false">
                    Выбрать
                </x-button-black>
            @endif
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-2">
        @if ($product->type == 'certificate')
            @if ($selectedCertificate)
                <x-button-black class="max-w-[360px]" @click="addToDataLayer" wire:click="addToCartAndRedirect">
                    Купить
                </x-button-black>
            @else
                <x-button-black class="max-w-[360px]" @click="openModalVariants('addToCartAndRedirect')">
                    Купить
                </x-button-black>
            @endif
        @else
            @if (!$isProductInCart)
                @if ($selectedVariant)
                    <x-button-black @click="addToDataLayer" wire:click="addToCart">
                        Добавить в корзину
                    </x-button-black>
                @else
                    <x-button-black @click="openModalVariants('addToCart')">
                        Добавить в корзину
                    </x-button-black>
                @endif
            @else
                <x-button-black>
                    <div id="editCountProducts" class="flex justify-center items-center text-lg font-bold gap-7">
                        <div wire:click="removeFromCart">-</div>
                        <div id="productsInCart">{{ $productQuantityInCart }}</div>
                        @if ($productQuantityInCart < $maxQuantity)
                            <div @click="addToDataLayer" wire:click="addToCart">+</div>
                        @else
                            <div></div>
                        @endif
                    </div>
                </x-button-black>
            @endif

            @if ($selectedVariant)
                <x-button-black @click="addToDataLayer" wire:click="addToCartAndRedirect">
                    Купить в один клик
                </x-button-black>
            @else
                <x-button-black @click="openModalVariants('addToCartAndRedirect')">
                    Купить в один клик
                </x-button-black>
            @endif
        @endif
    </div>

    {{-- @if ($showErrorMessage)
    @endif
    @if ($showErrorCertMessage)
    @endif --}}
</div>
