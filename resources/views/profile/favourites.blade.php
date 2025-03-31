@extends('layouts.app')
@section('title', 'Избранное')
@section('content')
    @if ($products->count() === 0)
        <div class="flex items-center justify-center h-full w-full pt-10 pb-10">
            <div class="text-center max-w-[320px]">
                <p class="text-[28px] font-semibold mb-2">Список пока пуст</p>
                <p class="mb-6">Здесь будут товары, которые вы добавили в избранное</p>

                <x-button-black
                    href="{{ route('catalog') }}"
                >
                    Перейти в каталог
                </x-button-black>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-x-1 max-md:gap-y-4 lg:grid-cols-3 xl:grid-cols-4 md:gap-2">
            @foreach($products as $product)
                @if ($product->show)
                    <livewire:product-card-redesign :product="$product" />
                @endif
            @endforeach
        </div>
    @endif
@endsection
