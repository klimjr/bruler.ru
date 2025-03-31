@extends('layouts.base')

@section('body')
    <x-header />
    @php
        $page = \App\Models\Page::where('route', request()->route()->uri)->first();
        $pageTitle =
            $page->h1 ??
            match (request()->route()->getName()) {
                'contacts' => 'Контакты',
                'delivery' => 'Доставка',
                'payment' => 'Оплата',
                'refund' => 'Возврат',
                'documents' => 'Документы',
                'preorder' => 'Предзаказ',
                default => 'Контакты',
            };
    @endphp

    <div class="custom-container mx-auto px-2.5 md:px-[50px] pt-[105px] pb-[25px] md:pb-[60px]">
        <a href="/">
            <x-icons.back-button class="w-[30px] md:w-[50px] h-[30px] md:h-[50px]" />
        </a>
    </div>

    <div class="custom-container mx-auto px-2.5 md:px-[50px]">
        <h1 class="text-3xl font-normal mb-[60px]">{{ $pageTitle }}</h1>
        <div class="w-full max-w-[1000px] mt-10 md:mt-0">
            @yield('content')

            @isset($slot)
                {{ $slot }}
            @endisset
        </div>
    </div>
    <x-footer />
@endsection
