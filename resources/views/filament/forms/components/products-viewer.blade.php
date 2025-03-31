<div class="grid gap-y-2" wire:ignore>
    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Товары</span>
    <div class="fi-fo-placeholder sm:text-sm grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($getRecord()->products as $product)
            @php
                $db_product = \App\Models\Product::find($product['id']) ?? 'null';
                $product_name = $product['name'] ?? 'null';
                $product_price = isset($product['type']) && $product['type'] === \App\Models\Product::TYPE_CERTIFICATE ? $product['certificate']['price'] : $product['price'];
                $product_quantity = $product['quantity'] ?? 'null';
                $product_variant = isset($product['variant']) ? $db_product->variants()->find($product['variant']) : 'null';
                $product_color = $product_variant instanceof \App\Models\ProductVariant ? \App\Models\Color::find($product_variant->color_id) : 'null';
                $product_size = $product_variant instanceof \App\Models\ProductVariant ? \App\Models\Size::find($product_variant->size_id) : 'null';
                $product_article = $product_variant instanceof \App\Models\ProductVariant ? $product_variant['article'] : null;
            @endphp
            <div class="flex flex-col space-y-2 text-center">
                @if($db_product->type === \App\Models\Product::TYPE_PRODUCT)
                    <img src="{{ $product_variant->getImageUrlAttribute() }}" alt="{{ $product_name }}">
                @elseif($db_product->type === \App\Models\Product::TYPE_CERTIFICATE)
                    <img src="{{ $product['certificate']['image'] }}" alt="{{ $product_name }}">
                @endif
                <p>{{ $db_product['name_en'] ?? 'null' }}</p>
                @if(isset($product_article))
                        <p>артикул: {{ $product_article }}</p>
                @endif
                <p>{{ $product_price }} ₽ ({{ $product_quantity }} шт.)</p>
                @if($db_product->type === \App\Models\Product::TYPE_PRODUCT)
                        <div class="flex gap-4 items-center justify-center">
                            <span>Цвет - {{ $product_color['name'] ?? 'null' }}</span>
                            <span>Размер - {{ $product_size['name'] ?? 'null' }}</span>
                        </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
