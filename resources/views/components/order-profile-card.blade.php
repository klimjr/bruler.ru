<div class="flex flex-col">
  <div class="flex justify-between items-start md:items-center space-x-2 w-full">
    <div class="flex space-x-3 md:space-x-6">
      <img alt="{{$product['name']}}" src="{{$product['image_url']}}" class="col-span-1 object-contain w-[60px] h-[76px] min-w-[60px] min-h-[76px] md:min-w-[150px] md:min-h-[188px] md:w-[150px] md:h-[188px]"/>
      <div class="flex flex-col space-y-1 md:space-y-2">
        <p class="main-text !text-[16px] md:!text-[20px]">{{ $product['name'] }}</p>
        @if($product['type'] === \App\Models\Product::TYPE_PRODUCT)
              <p class="small-text">размер: {{ $product['size']['name'] }}</p>
        @endif
      </div>
    </div>
    <p class="price-small">
      @if($product['quantity'] >= 2)
        <span>{{ $product['quantity'] }} x </span>
      @endif
      <span>{{ $product['price'] }} ₽</span>
    </p>
  </div>
  @if(!$isLast)
    <div class="h-[1px] md:h-[2px] w-full bg-primary !mt-4 md:!mt-8 !mb-3"></div>
  @endif
</div>
