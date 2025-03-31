@extends('layouts.app')
@section('title', 'Каталог')
@section('content')
    <div>
        <h2 class="text-[18px] md:text-[28px] text-center">Каталог</h2>
        @if (count($products) >= 1)
            <div class="flex flex-col space-y-4 mt-5 first:mt-0">
                <div class="grid grid-cols-2 gap-1 md:grid-cols-4 md:gap-2">
                    @foreach ($products as $product)
                        @if ($product->show)
                            <livewire:product-card-redesign :product="$product"/>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
