<div class="relative flex items-center w-[500px] border-t-2 border-[#F2F2F4]">
    <div class="w-[140px] h-[160px]">
        @if ($type === \App\Models\Product::TYPE_PRODUCT)
            <img class="object-cover w-full h-full px-[20px] py-[15px]" src="{{ $variant->getImageUrlAttribute() }}"
                alt="{{ $product->name }}" />
        @elseif($type === \App\Models\Product::TYPE_CERTIFICATE)
            <img class="object-cover w-full h-full px-[20px] py-[15px]" src="{{ $certificate['image'] }}"
                alt="{{ $product->name }}" />
        @endif
    </div>

    <div class="flex flex-col">
        <div class="text-xs md:text-lg">{{ $product->name_en }}</div>
        @if ($type === \App\Models\Product::TYPE_PRODUCT)
            <div class="text-sm text-[#757575]">Размер: <span
                    class="text-black">{{ $variant->size->name ?? 'L' }}</span>
            </div>
        @endif
        <div class="text-sm text-[#757575]">Количество: <span class="text-black">{{ $quantity }}</span>
        </div>
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
                        class="text-[#D0021B] text-xl font-semibold">{{ number_format($product->getDiscountedPrice() * ($quantity - 1), 0, '.', '.') }}₽</span>
                    <s
                        class="text-xl font-semibold">{{ number_format($product->price * ($quantity - 1), 0, '.', '.') }}₽</s>
                @else
                    <span>
                        <span class="text-[#757575] text-lg font-medium">Итого:</span>
                        <span
                            class="text-xl font-semibold">{{ number_format($product->price * ($quantity - 1), 0, '.', '.') }}₽</span>
                    </span>
                @endif
            @elseif($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                <span
                    class="text-[#757575] text-lg font-medium">Итого:</span>{{ number_format($certificate['price'], 0, '.', '.') }}₽
            @endif
        @else
            @if (isset($product->discount) && $product->discount != 0)
                <div class="flex items-center gap-x-2">
                    <span class="text-[#757575] text-lg font-medium">Сумма: </span>
                    <span
                        class="text-[#D0021B] text-xl font-semibold">{{ number_format($product->getDiscountedPrice() * $quantity, 0, '.', '.') }}₽</span>
                    <s class="text-xl font-semibold">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽</s>
                </div>
            @else
                <span>
                    <span class="text-[#757575] text-lg font-medium">Сумма:
                    </span>
                    @if ($type === \App\Models\Product::TYPE_CERTIFICATE)
                        <span
                            class="text-xl font-semibold">{{ number_format($certificate['price'] * $quantity, 0, '.', '.') }}₽
                        </span>
                    @else
                        <span
                            class="text-xl font-semibold">{{ number_format($product->price * $quantity, 0, '.', '.') }}₽
                        </span>
                    @endif
                </span>
            @endif
        @endif
    </div>
</div>
