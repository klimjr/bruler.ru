<a {{ $attributes }} class="product-card bg-white" href="{{ $url }}">
  <div class="product-card-image bg-white h-[188px] md:h-[500px] relative">
    <div class="mx-auto h-[188px] md:h-[500px] w-fit md:w-full relative">
        <img alt="product_image" class="w-full h-full object-contain" src="{{ $image }}"/>
    </div>
      <livewire:fire :product="$product"/>
  </div>
  <div class="product-card-content text-center mt-2 md:mt-4 mb-2 md:mb-4">
    <div class="product-card-name text">{{ $name }}</div>
    <div class="product-card-price price-small">{{ $price }} ₽</div>
  </div>
</a>
