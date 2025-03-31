<div
    x-data="{
         isVisible: true,
         isModalOpenCartItem: false,
         cart: [],
         is_free: false,

         removeYMProduct(quantity, product) {
             // При удалении карточки просто меняем isVisible:
             this.isVisible = false;
             this.addDataLayer(quantity, product);
         },

         removeOneYMProduct(quantity, product) {
             // Если количество стало меньше или равно 1, скрываем карточку:
             if (quantity <= 1) {
                 this.isVisible = false;
             }
             this.addDataLayer(1, product);
         },

         addOneYMProduct(product) {
             this.addDataLayer(null, product);
         },

         addDataLayer(quantity, product) {
             const productObject = {
                 id: product.id,
                 name: product.name_en || '',
                 price: product.price,
                 quantity: quantity || undefined,
             };
             if (window.dataLayer && Array.isArray(window.dataLayer)) {
                 window.dataLayer.push({
                     ecommerce: {
                        currencyCode: 'RUB',
                        click: { products: [productObject] },
                    },
                 });
             }
          }
    }"
    x-show="isVisible"
    class="border-b border-grey-200 pb-3"
>
    <!-- Если карточка продукта и требуется модальное окно выбора размера -->
    @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
        <div
            x-cloak
            x-show="isModalOpenCartItem"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 flex items-center justify-center"
        >
            <div id="profile-edit" class="relative bg-white rounded-2xl px-6 md:px-[32px] py-[28px] w-full max-w-[350px] z-50">
                <div class="absolute top-[16px] right-[16px] cursor-pointer" @click="isModalOpenCartItem = false">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.60743 18.3925L18.3925 6.60743M6.60742 6.60742L18.3925 18.3925" stroke="#757575"
                              stroke-width="1.875" stroke-linecap="square" />
                    </svg>
                </div>

                <div class="text-xl font-normal text-center">Доступные размеры:</div>

                <div class="flex flex-col items-center">
                    <livewire:product-variant-selector-redesign
                        :selected-color="$variant->color->id"
                        wire:key="product-variant-selector-{{ $product->id }}"
                        :product="$product"
                    />
                    <div>
                        <div class="text-xs md:text-sm text-[#757575] mt-2 underline cursor-pointer">
                            Размерная сетка
                        </div>
                    </div>

                    <x-button-black class="mt-3" wire:click="changeVariant" @click="isModalOpenCartItem = false">
                        Выбрать
                    </x-button-black>
                </div>
            </div>
            <div x-show="isModalOpenCartItem" class="fixed inset-0 bg-black-opacity z-60" x-cloak @click="isModalOpenCartItem = false"></div>
        </div>
@endif

<!-- Основное содержимое карточки -->
    <div class="grid grid-cols-[90px_auto_24px] gap-3">
        <a href="{{ $product->getRouteUrl() }}" class="w-[90px] h-[124px] bg-gray-100">
            @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                <img class="object-scale-down w-full h-full" src="{{ $variant->getImageUrlAttribute() }}"
                     alt="{{ $product->name }}" />
            @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                <img class="object-scale-down w-full h-full" src="{{ $certificate['image'] }}"
                     alt="{{ $product->name }}" />
            @endif
        </a>

        <div class="flex flex-col w-full h-full ">
            <a href="{{ $product->getRouteUrl() }}" class="inline-block text-[18px] font-bold mb-2">
                {{ $product->name_en }}
            </a>

            <div class="mb-3">
                @if ($is_free && $quantity === 1)
                    <span class="inline-block text-[18px]">0₽</span>
                @elseif($is_free && $quantity !== 1)
                    @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                        @if (isset($product->discount) && $product->discount != 0)
                            <span class="inline-block text-red text-[18px] mr-2">{{ number_format($product->getDiscountedPrice(), 0, '.', '.') }}₽</span>
                            <span class="inline-block text-color-111 text-[18px] line-through">{{ number_format($product->price, 0, '.', '.') }}₽</span>
                        @else
                            <span class="inline-block text-[18px]">{{ number_format($product->price, 0, '.', '.') }}₽</span>
                        @endif
                    @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                        {{ number_format($certificate['price'], 0, '.', '.') }}₽
                    @endif
                @else
                    @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                        @if (isset($product->discount) && $product->discount != 0)
                            <span class="inline-block text-red text-[18px] mr-2">{{ number_format($product->getDiscountedPrice() * $quantity, 0, '.', '.') }}₽</span>
                            <span class="inline-block text-color-111 text-[18px] line-through">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽</span>
                        @else
                            <span class="inline-block text-[18px]">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽</span>
                        @endif
                    @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                        {{ number_format($certificate['price'] * $quantity, 0, '.', '.') }}₽
                    @endif
                @endif
            </div>

            <div class="flex items-center mb-1">
                @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                    <div class="text-black mr-1">
                        Размер:
                        <span class="text-black">{{ $variant->size->name }}</span>
                    </div>
                    <div class="cursor-pointer" @click="isModalOpenCartItem = true">
                        <x-icons.pen-cart />
                    </div>
                @endif
            </div>

            <div class="flex items-center">
                <div class="text-black mr-1">Кол-во:</div>
                <div class="flex items-center select-none">
                    <div
                        wire:click="decrementQuantity"
                        @click="removeOneYMProduct({{ $quantity }}, @js($product))"
                    >
                        <x-icons.round-minus class="cursor-pointer" />
                    </div>
                    <div class="ml-1 mr-1">
                        @if ($is_free && $quantity !== 1)
                            {{ $quantity - 1 }}
                        @else
                            {{ $quantity }}
                        @endif
                    </div>
                    <div :class="{ 'hidden': {{ $quantity }} >= {{ $maxQuantity }} }"
                         @click="addOneYMProduct(@js($product))"
                         wire:click="incrementQuantity">
                        <x-icons.round-plus class="cursor-pointer" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:justify-between gap-4">
            <button
                class="cursor-pointer text-color-111"
                wire:click="removeProduct('{{ $cart_id }}')"
                @click="removeYMProduct(null, @js($product))"
            >
                <x-icons.delete-cart />
            </button>
        </div>
    </div>
</div>
