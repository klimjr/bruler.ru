@extends('layouts.app')
@section('title', 'Коллекция')
@section('content')
    <div>
        @if (isset($collectionsAndProducts) && $collectionsAndProducts)
            @foreach ($collectionsAndProducts as $collection)
                <div class="px-0 md:px-10 mb-20">
                    <div class="flex flex-col justify-center items-center mb-8">
                        <div class="text-base md:text-xl">{{ $collection->desc }}</div>
                        <div class="flex items-center gap-1 md:gap-2.5">
                            <div class="text-[18px] md:text-[28px] font-extrabold">{{ $collection->title }}</div>
                            <svg class="w-[40px] md:w-auto @if (!$collection->is_new) hidden @endif" width="75"
                                 height="33" viewBox="0 0 75 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="74" height="33" transform="translate(0.5)" fill="black" />
                                <path
                                    d="M9.32 26V7.28H12.908L21.046 19.76V7.28H24.634V26H21.046L12.908 13.52V26H9.32ZM28.2614 26V7.28H40.4814V10.582H31.7974V14.586H38.9214V17.888H31.7974V22.698H40.4814V26H28.2614ZM47.5233 26L42.0373 7.28H45.7553L49.2653 20.15L52.7753 7.306L56.4933 7.28L60.0033 20.15L63.5133 7.28H67.2313L61.7453 26H58.2613L54.6343 13.364L51.0073 26H47.5233Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex gap-9 justify-between mb-8 max-h-[340px] md:max-h-[700px]">
                        <div>
                            <img class="object-cover w-full h-full" src="/storage/{{ $collection->img1 }}" />
                        </div>
                        <div class="hidden md:block">
                            <img class="object-cover w-full h-full" src="/storage/{{ $collection->img2 }}" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-1 md:grid-cols-4 md:gap-2">
                        @foreach ($collection->products as $product)
                            @if ($product->show)
                                <livewire:product-card-redesign :product="$product" />
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            @foreach ($categoriesAndProducts as $categoryName => $products)
                @if (count($products) >= 1)
                    <div class="flex flex-col space-y-4 mt-5 first:mt-0">
                        <h2 class="text-[18px] md:text-[28px] text-center">{{ $categoryName }}</h2>
                        <div class="grid grid-cols-2 gap-1 md:grid-cols-4 md:gap-2">
                            @foreach ($products as $product)
                                @if ($product->show)
                                    <livewire:product-card-redesign :product="$product" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
@endsection
