@extends('layouts.account')

@section('content')
    <div class="flex flex-col">
        <h3 class="h3">
            История заказов
        </h3>
    </div>
    @if (count($orders) >= 1)
        <div class="h-[1px] md:h-[2px] w-full bg-primary mt-6 mb-0 md:mt-10 md:mb-3"></div>
    @endif
    <div class="mt-1 mb-24 grid grid-cols-1 gap-0 md:gap-[27px] md:mt-6">
        @if (count($orders) >= 1)
            @foreach ($orders->reverse() as $order)
                <div class="col-span-1">
                    <x-orders-profile-card :order="$order" :dbProducts="$order['db_products']" :url="$order->getRouteUrl()" />
                </div>
            @endforeach
        @else
            <div class="main-text">
                <p>к сожалению, ваша история заказов пуста.</p>
                <p>может, исправим это?</p>
            </div>
            <a href="{{ route('catalog') }}" class="w-full mt-4">
                <x-button class="min-w-full flex justify-center items-center space-x-2 h-[48px] md:!min-w-[256px]">
                    <span>коллекция</span>
                    <x-icons.arrow-right-medium />
                </x-button>
            </a>
        @endif
    </div>
@endsection
