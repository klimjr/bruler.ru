@extends('layouts.base')

@section('body')
    <x-header />

    <div class="custom-container mx-auto px-2.5 md:px-[50px] pt-[105px] pb-[25px] md:pb-[60px]">
        <a href="/">
            <x-icons.back-button class="w-[30px] md:w-[50px] h-[30px] md:h-[50px]" />
        </a>
    </div>
    <div class="custom-container mx-auto md:px-[50px] flex flex-col justify-center md:flex-row md:mb-28">
        @yield('content')

        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
    <x-footer />
@endsection
