@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.base')

@section('body')
    <x-header />
    <div class="custom-container mx-auto px-2.5 md:px-[50px] pt-[105px] pb-[25px] md:pb-[60px]">
        <a href="/">
            <x-icons.back-button class="w-[30px] md:w-[50px] h-[30px] md:h-[50px]" />
        </a>
    </div>
    <div class="custom-container mx-auto px-2.5 md:px-[50px]">
        @php
            $page = \App\Models\Page::where('route', request()->route()->uri)->first();
            $page_title = $page->h1 ?? 'аккаунт';
        @endphp
        {{-- <h1 class="text-3xl">{{ $page_title }}</h1> --}}
        <div class="">
            @yield('content')

            @isset($slot)
                {{ $slot }}
            @endisset
        </div>
    </div>
    <x-footer />
@endsection
