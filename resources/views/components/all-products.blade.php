<div {{ $attributes->merge(['class' => 'products mt-[95px]']) }} {{ $attributes }}>
    <div class="h1 my-5 md:my-16 px-2.5 md:px-[50px]">{{ $page->h1 ?? 'магазин' }}</div>
    <livewire:product-filter />
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mx-0 md:mx-10 lg:mx-16 xl:mx-20 md:gap-[27px] mt-3 md:mt-[50px]">
        @foreach ($products as $product)
            <div class="col-span-1">
                <livewire:product-card :product="$product" />
            </div>
        @endforeach
    </div>
</div>
