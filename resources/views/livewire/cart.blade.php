<div class="h-full" >
    @if ($allProducts == 0)
        <div class="flex flex-col justify-center h-full">
            <p class="text-center mt-10 text-xl">Корзина пуста</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($cart as $productId => $product)
                @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                    @livewire('cart-item-redesign', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => $product['variant'], 'certificate' => null, 'set_products' => null, 'is_free' => $product['is_free'], 'index' => $product['id'] . '_' . $product['variant']], key($product['id'] . '_' . $product['variant']))
                @endif

                @if ($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                    @livewire('cart-item-redesign', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => null, 'certificate' => $product['certificate'], 'set_products' => null, 'is_free' => $product['is_free'], 'index' => $product['id'] . '_' . $product['certificate']['price']], key($product['id'] . '_' . $product['certificate']['price']))
                @endif

                @if ($product['type'] === \App\Models\Product::TYPE_SET)
                    @livewire('cart-item-redesign', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => $product['variant'], 'certificate' => null, 'set_products' => $product['set_products'], 'is_free' => $product['is_free'], 'index' => $productId], key($productId))
                @endif
            @endforeach
        </div>
    @endif
</div>
