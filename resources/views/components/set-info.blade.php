<div>
    @foreach ($getRecord()->products as $productRecord)
        @if (array_key_exists('set_products', $productRecord) && $productRecord['set_products'])
            <div class="font-medium">Состав набора {{ $productRecord['name'] }}:</div>
            @foreach ($productRecord['set_products'] as $product_set)
                @php
                    $setVariant = App\Models\ProductVariant::find($product_set[0]['selectedVariant']);
                    $size = App\Models\Size::find($setVariant->size_id);
                @endphp
                <div>{{ $product_set[0]['name_en'] . ', Размер: ' . $size->name }}</div>
            @endforeach
            <br>
        @endif
    @endforeach
</div>
