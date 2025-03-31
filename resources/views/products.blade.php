@extends('layouts.app')
@section('title', 'Продукты')
@section('content')
    <div class="products px-8">
        <div class="h1 pt-10 pb-5 md:pb-16 md:pt-24">{{ $category->name }}</div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-[27px]">
            @foreach ($products as $product)
                @if($product->show)
                    <div class="col-span-1">
                        {{--          <x-product-card :product="$product" :url="$product->getRouteUrl()" :image="$product->getImageUrlAttribute()" :name="$product->name_en" :price="$product->price"/> --}}
                        <livewire:product-card :product="$product"/>
                    </div>
                @endif
            @endforeach
        </div>
        <a href="{{ route('catalog') }}"
           class="h2_nav text-right flex space-x-2 justify-end items-center mb-10 md:mb-20 mt-4 md:mt-10">
            <span>смотреть все</span>
            <x-icons.arrow-right-long/>
        </a>
    </div>

@endsection
