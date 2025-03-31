@extends('layouts.base')

@section('body')
    <x-order.header />

    <div class="container">
        @yield('content')

        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
@endsection
