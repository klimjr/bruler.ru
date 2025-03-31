@extends('layouts.app')
@section('content')
    <div class="grid lg:grid-cols-[minmax(460px,_40%)_60%] xl:grid-cols-[minmax(600px,_30%)_70%] lg:items-end gap-x-6 lg:overflow-hidden">
            <div
                class="w-auto"
                :class="isShowRunningTexts ? 'h-[600px)] md:h-[calc(100vh-96px)]' : 'h-[600px)] md:h-[calc(100vh-64px)]'"
            >
                <img
                    src="/storage/{{ $look->image_inside }}"
                    alt="{{ $look['alt'] ?? '' }}"
                    class="object-cover w-full h-full"
                />
            </div>

            <!-- Коллекция продуктов -->

            <div class="pt-6">
                <p class="text-[18px] md:text-[28px] mb-4 md:mb-6 max-lg:text-center">Вещи из образа</p>

                <div class="grid grid-cols-2 gap-y-4 gap-x-1 md:grid-cols-4 md:gap-y-2 md:gap-x-2 md:hidden" wire:ignore>
                    @foreach ($products as $product)
                        @if ($product->show)
                            <livewire:product-card-redesign :product="$product" />
                        @endif
                    @endforeach
                </div>

                <x-look-slider :products="$products" />
            </div>
        </div>

        <livewire:ready-made-main :slug="$slug" />
    </div>
@endsection
