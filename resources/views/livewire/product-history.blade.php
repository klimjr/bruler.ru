<div>
    @foreach ($insert_products as $product)
        <div class="relative flex {{ !$loop->last ? 'border-b border-grey-200 mb-4 pb-3' : '' }}">
            <div class="flex-shrink-0 w-[60px] h-[82px] mr-3 md:mr-4">
                <img class="object-cover w-full h-full" src="/storage/{{ $product['image'] }}">
            </div>

            <div class="text-sm flex flex-col md:flex-row md:items-center md:justify-between md:w-full">
                <div class="max-md:mb-1">
                    <div class="md:text-lg font-semibold md:font-bold mb-1 md:mb-2">{{ $product['name'] }}</div>
                    <div class="md:text-lg font-semibold md:font-normal mb-1 md:mb-3">
                        {{ number_format($product['price'], 0, '.', '.') }}₽
                    </div>

                    <div class="md:text-base mb-1">
                        Размер:
                        <span class="ml-1">{{ $product['size'] }}</span>
                    </div>
                    <div class="md:text-base">
                        Кол-во:
                        <span class="ml-1">{{ $product['quantity'] }}</span>
                    </div>
                </div>

                <div class="md:text-right">
                    <div class="md:text-base mb-1 md:mb-4">
                        Номер заказа:
                        <span class="ml-1">{{ $product['order'] }}</span>
                    </div>
                    <div class="md:text-base">
                        Дата:
                        <span class="ml-1">{{ $product['created'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
