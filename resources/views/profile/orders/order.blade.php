@extends('layouts.account')

@section('content')
  <div class="flex flex-col md:items-center justify-between space-y-5 md:space-y-0 md:flex-row">
    <h3 class="h3">
      история заказов
    </h3>
    <p class="h3 !text-gray">
      заказ № {{ $order->id }}
    </p>
  </div>
  <div class="h-[1px] md:h-[2px] w-full bg-primary mt-4 md:mt-10 mb-3 hide_in_mobile"></div>
  <div class="mt-4 md:mt-10 mb-4 md:mb-24 grid grid-cols-1 gap-5 md:gap-10">
    <div class="flex flex-col space-y-4">
      <div class="flex items-center space-x-2">
        <p class="min-w-[200px] price-small">дата покупки</p>
        <span class="main-text !text-[12px] md:!text-[20px]">{{ $order->created_at->locale('ru')->isoFormat('D MMMM Y') }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <p class="min-w-[200px] price-small">получатель</p>
        <span class="main-text !text-[12px] md:!text-[20px]">{{ $order->user->name }} {{ $order->user->last_name }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <p class="min-w-[200px] price-small">адрес доставки</p>
        <span class="main-text !text-[12px] md:!text-[20px]">Город {{ $order->city }}</span>
      </div>
    </div>
    <h3 class="h3 !text-gray">сумма заказа</h3>
    <div class="flex flex-col space-y-4">
      <div class="flex items-center justify-between">
        <p class="main-text !text-[16px] md:!text-[20px]">сумма</p>
        <p class="price-small !text-[20px]">{{ $order->price_order }} ₽</p>
      </div>
      @if(isset($order->promocode))
        <label for="promocode" class="block leading-5 space-y-3 flex-col hide_in_mobile">
          <span class="form-label main-text">промокод</span>
          <div class="relative w-full md:w-fit h-fit">
            <input value="{{ $order->promocode['code'] }}" id="promocode" type="text" disabled autofocus class="form-input !h-[29px] !w-full md:!w-[397px]" />
            <span class="promocode_discount price-small">
              -{{ $order->promocode['discount'] }} %
            </span>
          </div>
        </label>
        <div class="flex justify-between">
          <div class="text-primary text !text-[16px] md:!text-[20px]">cумма с учетом скидки</div>
          <div class="price-small !text-[20px] whitespace-nowrap">{{ $order->price_with_promocode }} ₽</div>
        </div>
      @endif
      <div class="flex justify-between">
        <div class="text-primary text !text-[16px] md:!text-[20px]">стоимость доставки</div>
        <div class="price-small !text-[20px] whitespace-nowrap">{{ $order->delivery_price }} ₽</div>
      </div>
      <div class="flex justify-between items-center">
        <div class="text-primary h3">итого</div>
        <div class="!h3 price-small !text-[20px] md:!text-[32px]">{{ $order->price }} ₽</div>
      </div>
    </div>
    <h3 class="h3 !text-gray">состав заказа</h3>
    <div class="flex flex-col w-full space-y-0 md:space-y-4">
      @foreach($order->products as $product)
          <?php
          $db_product = $order->db_products->firstWhere('id', $product['id']);
          ?>
        <x-order-profile-card :product="$product" :dbProduct="$db_product" :isLast="$loop->last"/>
      @endforeach
        <a class="h2_nav !mb-6 !mt-2 flex items-center justify-end space-x-2 !px-6 hide_in_desktop" href="/">
          <span>вернуться к покупкам</span>
          <x-icons.arrow-right-medium/>
        </a>
    </div>
  </div>
@endsection
