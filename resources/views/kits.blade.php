@extends('layouts.app')
@section('title', 'Комплекты')
@section('content')
    <div>
        {{--        <div class="text-[18px] md:text-[28px] text-center mb-2">--}}
        {{--            <div>Всего товаров: {{ count($products) }}</div>--}}
        {{--            <div>{{ $kits_count }} комплектов</div>--}}
        {{--        </div>--}}
        @if($slug)
            <h1 class="text-[18px] md:text-[28px] text-center mb-2">Комплект</h1>
        @else
            <h1 class="text-[18px] md:text-[28px] text-center mb-2">Комплекты</h1>
        @endif
        <div class="grid grid-cols-2 md:grid-cols-3 gap-[27px]">
            @foreach ($products as $product)
                @if ($product->show)
                    <div class="col-span-1">
                        <livewire:product-card-redesign :product="$product"/>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

@endsection
