@extends('layouts.base')

@section('body')
    <x-header />

    <div class="flex-grow pt-[var(--headerSize)]">
        <div class="h-full w-full pt-1 md:pt-2">
            @yield('content')

            @isset($slot)
                {{ $slot }}
            @endisset
        </div>
    </div>
    <x-footer/>
@endsection
