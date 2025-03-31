@extends('layouts.app')
@section('title', 'Ошибка заказа!')
@section('content')
    <div class="pt-[125px] pb-[100px] md:pb-[160px] md:pt-[225px]">
        <div class="flex justify-end items-center px-6 pb-3 md:pb-24 lg:px-24 hide_in_mobile">
            <a class="h2 flex items-center space-x-2" href="/">
                <span>вернуться к покупкам</span>
                <x-icons.arrow-right-long/>
            </a>
        </div>
        <div class="flex flex-col items-center justify-center space-y-4">
            <h2 class="h1">что-то пошло не так?</h2>
            <p class="main-text">напишите в поддержку</p>
            <div class="flex flex-col space-y-2 items-start">
                <a href="https://t.me/bruler_support" class="main-text flex items-center space-x-2">
                    <x-icons.telegram/>
                    <span>Telegram</span>
                </a>
                <a href="https://wa.me/message/3VVEZGYWKTN6L1" class="main-text flex items-center space-x-2">
                    <x-icons.whatsapp/>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
@endsection
