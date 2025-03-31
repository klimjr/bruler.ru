@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => env('APP_FRONTEND_URL')])
            <img src="{{asset('images/logo/logo.svg')}}" alt="{{config('app.name')}}">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            С любовью и стилем,
            <br>
            Команда Bruler d’amour
        @endcomponent
    @endslot
@endcomponent
